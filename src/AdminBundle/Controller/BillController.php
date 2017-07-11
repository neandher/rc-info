<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Bill;
use AdminBundle\Entity\BillRemessa;
use AdminBundle\Entity\BillStatus;
use AdminBundle\Event\BillEvents;
use AdminBundle\Form\Type\BillFileRetornoType;
use AdminBundle\Form\Type\BillMonthlyInvoiceType;
use AdminBundle\Form\Type\BillType;
use AppBundle\Event\FlashBagEvents;
use Cnab\Factory;
use Cnab\Retorno\Cnab240\Detalhe;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SiteBundle\Entity\Customer;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\File\File;
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

        $billMonthlyInvoiceForm = $this->createForm(BillMonthlyInvoiceType::class);
        $billFileRetornoForm = $this->createForm(BillFileRetornoType::class);

        return $this->render('admin/bill/index.html.twig', [
            'bills' => $bills,
            'bill_status' => $billstatus,
            'pagination' => $pagination,
            'delete_forms' => $deleteForms,
            'billMonthlyInvoice' => $billMonthlyInvoiceForm->createView(),
            'billRetornoFile' => $billFileRetornoForm->createView()
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

            $this->get('event_dispatcher')->dispatch(BillEvents::CREATE_SUCCESS, new GenericEvent($bill));

            $em = $this->getDoctrine()->getManager();
            $em->persist($bill);
            $em->flush();

            $genericEvent = new GenericEvent($bill->getBillRemessa());
            $genericEvent->setArgument('company', $this->get('app.admin.company_find')->find());

            $this->get('event_dispatcher')->dispatch(BillEvents::CREATE_COMPLETED, $genericEvent);

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
            'validation_groups' => ['Default'],
            'is_edit' => true
        ]);

        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->setBillStatus($bill);

            $em = $this->getDoctrine()->getManager();
            $em->persist($bill);
            $em->flush();

            $genericEvent = new GenericEvent($bill->getBillRemessa());
            $genericEvent->setArgument('company', $this->get('app.admin.company_find')->find());

            $this->get('event_dispatcher')->dispatch(BillEvents::UPDATE_COMPLETED, $genericEvent);

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

            if ($bill->getBillRemessa()->getSent()) {
                $this->get('app.util.flash_bag')->newMessage(
                    FlashBagEvents::MESSAGE_TYPE_ERROR,
                    'Não é possível deletar uma cobrança em que o arquivo de remessa já foi enviado.'
                );
                return $this->redirectToRoute('admin_bill_index', $pagination->getRouteParams());
            }

            $em = $this->getDoctrine()->getManager();

            if ($bill->getBillRemessa()->getBills()->count() > 1) {

                $bill->getBillRemessa()->removeBill($bill);

                $genericEvent = new GenericEvent($bill->getBillRemessa());
                $genericEvent->setArgument('company', $this->get('app.admin.company_find')->find());

                $this->get('event_dispatcher')->dispatch(BillEvents::DELETE_COMPLETED, $genericEvent);
            } else {
                $em->remove($bill->getBillRemessa());
            }

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
     * @Route("/{id}/boleto/", requirements={"id" : "\d+"}, name="admin_bill_boleto")
     * @param Request $request
     * @param Bill $bill
     * @return Response
     */
    public function getBoleto(Request $request, Bill $bill)
    {
        $inline = $request->query->get('inline', false);
        $pagination = $this->get('app.util.pagination')->handle($request, Bill::class);
        $response = $this->get('app.admin.bill_boleto')->download($bill, $inline);

        if (!$response) {

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_ERROR,
                'Houve um erro ao baixar o arquivo.'
            );

            return $this->redirectToRoute('admin_bill_index', $pagination->getRouteParams());
        }

        return $response;
    }

    /**
     * @Route("/monthInvoice", name="admin_bill_monthly_invoice")
     * @Method("POST")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function monthlyInvoice(Request $request)
    {
        $form = $this->createForm(BillMonthlyInvoiceType::class);

        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {

            $em = $this->getDoctrine()->getManager();

            /** @var \DateTime $date */
            $date = $form->getData()['date'];

            $customers = $this->getDoctrine()->getRepository(Customer::class)->findAllCustom();
            $billStatus = $this->getDoctrine()->getRepository(BillStatus::class)
                ->findOneBy(['referency' => BillStatus::BILL_STATUS_EM_ABERTO]);

            $billRemessa = new BillRemessa();
            $billRemessa
                ->setDescription('Remessa de Fatura Mensal ' . $date->format('m/Y'))
                ->setSent(false);

            $bill = null;

            /** @var Customer $customer */
            foreach ($customers as $customer) {

                $dueDateAt = new \DateTime();
                $dueDateAt->setDate($date->format('Y'), $date->format('m'), $customer->getBillPayDay());

                $bill = new Bill();
                $bill->setDescription('Cobrança de Fatura Mensal')
                    ->setAmount($customer->getBillAmount())
                    ->setDueDateAt($dueDateAt)
                    ->setCustomer($customer)
                    ->setBillStatus($billStatus)
                    ->setBillRemessa($billRemessa);

                $em->persist($bill);
                $billRemessa->addBill($bill);
            }

            $em->flush();

            $company = $this->get('app.admin.company_find')->find();

            $genericEvent = new GenericEvent($billRemessa);
            $genericEvent->setArgument('company', $company);

            $this->get('event_dispatcher')->dispatch(BillEvents::CREATE_COMPLETED, $genericEvent);

        } else {
            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_ERROR,
                'Houve um erro ao gerar fatura mensal.'
            );
        }
        return $this->redirectToRoute('admin_bill_index');
    }

    /**
     * @Route("/retornoFile", name="admin_bill_retorno_file")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function retornoFile(Request $request)
    {
        $form = $this->createForm(BillFileRetornoType::class);

        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {

            /** @var File $file */
            $file = $form->getData()['file'];

            $fileConstraint = new \Symfony\Component\Validator\Constraints\File([
                'mimeTypes' => ['text/plain'],
                'mimeTypesMessage' => 'Arquivo inválido!',
            ]);

            $errorList = $this->get('validator')->validate($file, $fileConstraint);

            if (!(0 === count($errorList))) {
                $this->get('app.util.flash_bag')->newMessage(
                    FlashBagEvents::MESSAGE_TYPE_ERROR,
                    $errorMessage = $errorList[0]->getMessage()
                );
                return $this->redirectToRoute('admin_bill_index');
            }

            $em = $this->getDoctrine()->getManager();

            $billStatus = $this->getDoctrine()->getRepository(BillStatus::class)
                ->findOneBy(['referency' => BillStatus::BILL_STATUS_PAGO]);

            $cnabFactory = new Factory();
            $arquivo = $cnabFactory->createRetorno($file->getPathname());
            $detalhes = $arquivo->listDetalhes();

            $totalBaixa = 0;
            $msgBaixa = 'Arquivo processado com sucesso!';
            $msgBaixa .= '<br><br>';

            /** @var Detalhe $detalhe */
            foreach ($detalhes as $detalhe) {

                if ($detalhe->getValorRecebido() > 0) {

                    $nossoNumero = $detalhe->getNossoNumero();
                    $valorRecebido = $detalhe->getValorRecebido();
                    $dataPagamento = $detalhe->getDataOcorrencia();

                    $bill = $this->getDoctrine()->getRepository(Bill::class)->findOneBy(['id' => $nossoNumero]);

                    if ($bill) {

                        $bill->setPaymentDateAt($dataPagamento)
                            ->setAmountPaid($valorRecebido)
                            ->setBillStatus($billStatus);

                        $em->persist($bill);

                        $msgBaixa .= 'Cliente: ' . $bill->getCustomer()->getName() . '<br>';
                        $msgBaixa .= 'Valor recebido: ' . number_format($valorRecebido, 2, ',', '.') . '<br>';
                        $msgBaixa .= 'Data de pagamento:' . $dataPagamento->format('d/m/Y');
                        $msgBaixa .= '<br><br>';

                        $totalBaixa++;
                    }
                }
            }

            $msgBaixa .= 'Total de baixas efetuadas: ' . $totalBaixa;

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                $msgBaixa
            );

            $em->flush();
        }

        return $this->redirectToRoute('admin_bill_index');
    }
}