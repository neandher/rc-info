<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Bill;
use AdminBundle\Entity\BillStatus;
use AdminBundle\Entity\Company;
use AdminBundle\Form\Type\BillType;
use AppBundle\Event\FlashBagEvents;
use Carbon\Carbon;
use Eduardokum\LaravelBoleto\Boleto\Banco\Bancoob;
use Eduardokum\LaravelBoleto\Boleto\Banco\Caixa;
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
     * @param Bill $bill
     * @return Response
     */
    public function boleto(Bill $bill)
    {
        $company = new Company();

        $beneficiario = new Pessoa([
            'nome' => $company->getNomeFantasia(),
            'endereco' => $company->getAddressStreet(),
            'cep' => $company->getPostcode(),
            'uf' => $company->getUf(),
            'cidade' => $company->getCity(),
            'documento' => $company->getCnpj(),
        ]);

        $pagador = new Pessoa([
            'nome' => $bill->getCustomer()->getName(),
            'endereco' => $bill->getCustomer()->getMainAddress()->getStreet(),
            'bairro' => $bill->getCustomer()->getMainAddress()->getDistrict(),
            'cep' => $bill->getCustomer()->getMainAddress()->getPostcode(),
            'uf' => $bill->getCustomer()->getMainAddress()->getUf()->getSigla(),
            'cidade' => $bill->getCustomer()->getMainAddress()->getCity(),
            'documento' => $bill->getCustomer()->getCnpj(),
        ]);

        $boletoArray = [
            'logo' => false,
            'dataVencimento' => new Carbon($bill->getDueDateAt()->format('Y/m/d')),
            'valor' => $bill->getAmount(),
            'multa' => false,
            'juros' => false,
            'numero' => $bill->getId(),
            'numeroDocumento' => $bill->getId(),
            'pagador' => $pagador,
            'beneficiario' => $beneficiario,
            'carteira' => $company->getBoletoCarteira(),
            'codigoCliente' => $company->getBoletoCodigoCliente(),
            'agencia' => $company->getAgencia(),
            'conta' => $company->getBoletoCodigoCliente(),
            'descricaoDemonstrativo' => [
                'MULTA DE R$: 4,94 APÓS : ' . $bill->getDueDateAt()->format('d/m/Y'),
                'JUROS DE R$: 0,81 AO DIA',
                'NÃO RECEBER APÓS 120 DIAS DO VENCIMENTO',
                'ATENÇÃO após efetuar o pagamento entre em contato com nosso escritório e retire sua senha de liberação 33499130',
                'Título sujeito a protesto | Link para atualização de vencimento | bloquetoexpresso.caixa.gov.br'
            ],
            'instrucoes' => [
                'MULTA DE R$: 4,94 APÓS : ' . $bill->getDueDateAt()->format('d/m/Y'),
                'JUROS DE R$: 0,81 AO DIA',
                'NÃO RECEBER APÓS 120 DIAS DO VENCIMENTO',
                'Link para atualização de vencimento',
                'bloquetoexpresso.caixa.gov.br'
            ],
            'aceite' => $company->getBoletoAceite(),
            'especieDoc' => $company->getBoletoEspecieDoc(),
        ];

        $boleto = new Caixa($boletoArray);
        
        $dadosBoleto = $boleto->toArray();
        $dadosBoleto['imprimir_carregamento'] = false;

        $html = new Html($dadosBoleto);
        $dadosBoleto['css'] = $html->writeCss();
        $dadosBoleto['codigo_barras'] = $html->getImagemCodigoDeBarras($dadosBoleto['codigo_barras']);

        $download = false;

        $pdf = new Pdf();
        $pdf->addBoleto($boleto);
        $pdf_inline = $pdf->gerarBoleto($pdf::OUTPUT_STRING);

        return new Response(
            $pdf_inline,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => ($download ? 'attachment; ' : '')
                    . 'inline; filename="boleto.pdf"'
            ]
        );

        //return $this->render('admin/boleto/_boleto.html.twig', $dadosBoleto);

        /*$html = $this->render('admin/boleto/_boleto.html.twig', $dadosBoleto);

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => ($download ? 'attachment; ' : '')
                    . 'filename="' . 'boleto' . '.pdf"'
            ]
        );*/
    }
}