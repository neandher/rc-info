<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Bill;
use AdminBundle\Entity\BillStatus;
use AdminBundle\Form\Type\BillType;
use AppBundle\Event\FlashBagEvents;
use Carbon\Carbon;
use Eduardokum\LaravelBoleto\Boleto\Banco\Bancoob;
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
        //var_dump($this->get('assets.packages')->getUrl('/site/assets/images/client-logo3.png'));
        //exit;
        $boletoArray = [
            'logo' => 'e:/web/rc-info/web/site/assets/images/client-logo3.png', // Logo da empresa
            'dataVencimento' => new Carbon($bill->getDueDateAt()->format('Y/m/d')),
            'valor' => $bill->getAmount(),
            'multa' => 0, // porcento
            'juros' => 0, // porcento ao mes
            'juros_apos' => 1, // juros e multa após
            'diasProtesto' => false, // protestar após, se for necessário
            'numero' => 1,
            'numeroDocumento' => 1,
            'pagador' => $pagador, // Objeto PessoaContract
            'beneficiario' => $beneficiario, // Objeto PessoaContract
            'agencia' => 9999, // BB, Bradesco, CEF, HSBC, Itáu
            'agenciaDv' => 9, // se possuir
            'conta' => 99999, // BB, Bradesco, CEF, HSBC, Itáu, Santander
            'contaDv' => 9, // Bradesco, HSBC, Itáu
            'carteira' => 3, // BB, Bradesco, CEF, HSBC, Itáu, Santander
            'convenio' => 9999999, // BB
            'variacaoCarteira' => 99, // BB
            'range' => 99999, // HSBC
            'codigoCliente' => 99999, // Bradesco, CEF, Santander
            'ios' => 0, // Santander
            'descricaoDemonstrativo' => ['msg1', 'msg2', 'msg3'], // máximo de 5
            'instrucoes' => ['inst1', 'inst2'], // máximo de 5
            'aceite' => 1,
            'especieDoc' => 'DM',
        ];

        $boleto = new Bancoob($boletoArray);

        //$boleto->renderPDF();
        //$boleto->renderHTML();

        // Os dois métodos aceita como parâmetro 2 boleano.
        // 1º Se True após renderizado irá mostrar a janela de impressão. O Valor default é false.
        // 2º Se False irá esconder as instruções de impressão. O valor default é true
        //$boleto->renderPDF(true, false); // mostra a janela de impressão e esconde as instruções de impressão
        
        return new Response($boleto->renderHTML(false, $this->container));
    }
}