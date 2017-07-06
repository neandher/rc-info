<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Bill;
use AdminBundle\Entity\BillStatus;
use AdminBundle\Event\BillEvents;
use AdminBundle\Form\Type\BillType;
use AppBundle\Event\FlashBagEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\GenericEvent;
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
        $bill = $this->getDoctrine()->getRepository(Bill::class)->findOneBy(['id' => 7]);

        $this->get('app.admin.bill_remessa')->save($bill);

        exit;

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

            $this->get('event_dispatcher')->dispatch(BillEvents::CREATE_SUCCESS, new GenericEvent($bill));

            $em = $this->getDoctrine()->getManager();
            $em->persist($bill);
            $em->flush();

            $this->get('event_dispatcher')->dispatch(BillEvents::CREATE_COMPLETED, new GenericEvent($bill));

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

            if ($bill->getBillRemessa()->getSent()) {

                $this->get('app.util.flash_bag')->newMessage(
                    FlashBagEvents::MESSAGE_TYPE_ERROR,
                    'Não é possível alterar uma cobrança após o arquivo de remessa já ter sido enviado.'
                );

                return $this->redirectToRoute('admin_bill_edit', array_merge(
                    ['id' => $bill->getId()],
                    $pagination->getRouteParams()
                ));
            }

            $this->setBillStatus($bill);

            $this->get('event_dispatcher')->dispatch(BillEvents::UPDATE_SUCCESS, new GenericEvent($bill));

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
    public function getBoleto(Request $request, Bill $bill)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Bill::class);
        $response = $this->get('app.admin.bill_boleto')->download($bill);

        if (!$response) {

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_ERROR,
                'Houve um erro ao baixar o arquivo.'
            );

            return $this->redirectToRoute('admin_bill_index', $pagination->getRouteParams());
        }

        return $response;
    }
}