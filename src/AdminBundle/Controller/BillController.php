<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Bill;
use AdminBundle\Entity\BillStatus;
use AdminBundle\Form\Type\BillType;
use AppBundle\Event\FlashBagEvents;
use Carbon\Carbon;
use Eduardokum\LaravelBoleto\Boleto\Banco\Bancoob;
use Eduardokum\LaravelBoleto\Boleto\Render\Html;
use Eduardokum\LaravelBoleto\Boleto\Render\Pdf;
use Eduardokum\LaravelBoleto\Pessoa;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BillController
 * @package AppBundle\Controller\Admin
 *
 * @Route("/bill")
 */
class BillController extends BaseController
{
    /**
     * @Route("/", name="admin_bill_index")
     * @Method({"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Bill::class);

        $bills = $this->getDoctrine()->getRepository(Bill::class)->findLatest($pagination);

        $deleteForms = [];
        foreach ($bills as $bill) {
            $deleteForms[$bill->getId()] = $this->createDeleteForm($bill)->createView();
        }

        $billstatus = $this->getDoctrine()->getRepository(BillStatus::class)->findAll();

        return $this->render('admin/bill/index.html.twig', [
            'bills' => $bills,
            'bill_status' => $billstatus,
            'pagination' => $pagination,
            'delete_forms' => $deleteForms
        ]);
    }

    /**
     * @Route("/new", name="admin_bill_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Bill::class);

        $bill = new Bill();

        $form = $this->createForm(BillType::class, $bill);
        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->setBillStatus($bill);

            $em = $this->getDoctrine()->getManager();
            $em->persist($bill);
            $em->flush();

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_INSERTED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_bill_new',
                'admin_bill_edit',
                ['id' => $bill->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_bill_index');
        }

        return $this->render('admin/bill/new.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/edit", requirements={"id" : "\d+"}, name="admin_bill_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Bill $bill
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Bill $bill, Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Bill::class);

        $form = $this->createForm(BillType::class, $bill, [
            'validation_groups' => ['Default']
        ]);

        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->setBillStatus($bill);

            $em = $this->getDoctrine()->getManager();
            $em->persist($bill);
            $em->flush();

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_UPDATED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_bill_new',
                'admin_bill_edit',
                ['id' => $bill->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_bill_index');
        }

        return $this->render('admin/bill/edit.html.twig', [
            'bill' => $bill,
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/delete", requirements={"id" : "\d+"}, name="admin_bill_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Bill $bill
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletAction(Request $request, Bill $bill)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Bill::class);

        $form = $this->createDeleteForm($bill);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($bill);
            $em->flush();

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_DELETED
            );
        } else {
            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_ERROR,
                FlashBagEvents::MESSAGE_ERROR_DELETED
            );
        }

        return $this->redirectToRoute('admin_bill_index', $pagination->getRouteParams());
    }

    /**
     * @param Bill $bill
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm(Bill $bill)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_bill_delete', ['id' => $bill->getId()]))
            ->setMethod('DELETE')
            ->setData($bill)
            ->getForm();
    }

    private function setBillStatus(Bill $bill)
    {
        $status = BillStatus::BILL_STATUS_PAGO;

        if ($bill->getPaymentDateAt() === null && $bill->getAmountPaid() === null) {
            $status = BillStatus::BILL_STATUS_EM_ABERTO;
        }

        $bill->setBillStatus($this->getDoctrine()->getRepository(BillStatus::class)->findOneByReferency($status));
    }

    /**
     * @Route("/{id}/boleto", requirements={"id" : "\d+"}, name="admin_bill_boleto")
     * @param Request $request
     * @param Bill $bill
     * @return Response
     */
    public function boleto(Request $request, Bill $bill)
    {
        $beneficiario = new Pessoa([
            'nome' => 'RC Informática',
            'endereco' => 'Rua um, 123',
            'cep' => '99999-999',
            'uf' => 'ES',
            'cidade' => 'Vila Velha',
            'documento' => '99.999.999/9999-99',
        ]);

        $pagador = new Pessoa([
            'nome' => $bill->getCustomer()->getName(),
            'endereco' => 'Rua um, 123',
            'bairro' => 'Bairro',
            'cep' => '99999-999',
            'uf' => 'UF',
            'cidade' => 'CIDADE',
            'documento' => '999.999.999-99',
        ]);

        $boletoArray = [
            'logo'                   => $this->getParameter('kernel.project_dir') . '/web/site/assets/images/client-logo3.png',
            'dataVencimento'         => new Carbon($bill->getDueDateAt()->format('Y/m/d')),
            'valor'                  => $bill->getAmount(),
            'multa'                  => false,
            'juros'                  => false,
            'numero'                 => 1,
            'numeroDocumento'        => 1,
            'pagador'                => $pagador,
            'beneficiario'           => $beneficiario,
            'carteira'               => 1,
            'agencia'                => 1111,
            'convenio'               => 123123,
            'conta'                  => 22222,
            'descricaoDemonstrativo' => ['demonstrativo 1', 'demonstrativo 2', 'demonstrativo 3'],
            'instrucoes'             => ['instrucao 1', 'instrucao 2', 'instrucao 3'],
            'aceite'                 => 'S',
            'especieDoc'             => 'DM',
        ];

        $boleto = new Bancoob($boletoArray);
        
        $dadosBoleto = $boleto->toArray();
        $dadosBoleto['imprimir_carregamento'] = true;
        
        $html = new Html($dadosBoleto);
        $dadosBoleto['css'] = $html->writeCss();
        $dadosBoleto['codigo_barras'] = $html->getImagemCodigoDeBarras($dadosBoleto['codigo_barras']);

        /*$pdf = new Pdf();
        $pdf->addBoleto($boleto);
        $pdf_inline = $pdf->gerarBoleto($pdf::OUTPUT_STRING);

        $download = false;

        return new Response(
            $pdf_inline,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => ($download ? 'attachment; ' : '')
                    . 'inline; filename="bancoob.pdf"'
            ]
        );*/

        return $this->render('admin/boleto/_boleto.html.twig', $dadosBoleto);
    }
}