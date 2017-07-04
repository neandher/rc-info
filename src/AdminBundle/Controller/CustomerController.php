<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\Type\CustomerType;
use AppBundle\Event\FlashBagEvents;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SiteBundle\Entity\Customer;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Event\UserEvents;

/**
 * Class CustomerController
 * @package AppBundle\Controller\Admin
 *
 * @Route("/customer")
 */
class CustomerController extends BaseController
{
    /**
     * @Route("/", name="admin_customer_index")
     * @Method({"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Customer::class);

        $customers = $this->getDoctrine()->getRepository(Customer::class)->findLatest($pagination);

        $deleteForms = [];
        foreach ($customers as $customer) {
            $deleteForms[$customer->getId()] = $this->createDeleteForm($customer)->createView();
        }

        return $this->render('admin/customer/index.html.twig', [
            'customers' => $customers,
            'pagination' => $pagination,
            'delete_forms' => $deleteForms
        ]);
    }

    /**
     * @Route("/new", name="admin_customer_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Customer::class);

        $customer = new Customer();

        $form = $this->createForm(CustomerType::class, $customer);
        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->get('event_dispatcher')->dispatch(
                UserEvents::REGISTRATION_SUCCESS,
                new GenericEvent($customer->getSiteUser())
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->flush();

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_INSERTED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_customer_new',
                'admin_customer_edit',
                ['id' => $customer->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_customer_index');
        }

        return $this->render('admin/customer/new.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/edit", requirements={"id" : "\d+"}, name="admin_customer_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Customer $customer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Customer $customer, Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Customer::class);

        $form = $this->createForm(CustomerType::class, $customer, [
            'is_edit' => true,
            'validation_groups' => []
        ]);

        $this->addDefaultSubmitButtons($form);

        $originalCustomerAddresses = new ArrayCollection();
        foreach ($customer->getCustomerAddresses() as $customerAddress) {
            $originalCustomerAddresses->add($customerAddress);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->get('event_dispatcher')->dispatch(
                UserEvents::REGISTRATION_SUCCESS,
                new GenericEvent($customer->getSiteUser())
            );

            $em = $this->getDoctrine()->getManager();

            foreach ($originalCustomerAddresses as $customerAddress) {
                if (false === $customer->getCustomerAddresses()->contains($customerAddress)) {
                    $customerAddress->setCustomer(null);
                    $em->remove($customerAddress);
                }
            }

            $em->persist($customer);
            $em->flush();

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_UPDATED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_customer_new',
                'admin_customer_edit',
                ['id' => $customer->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_customer_index');
        }

        return $this->render('admin/customer/edit.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/delete", requirements={"id" : "\d+"}, name="admin_customer_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Customer $customer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletAction(Request $request, Customer $customer)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Customer::class);

        $form = $this->createDeleteForm($customer);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($customer);
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

        return $this->redirectToRoute('admin_customer_index', $pagination->getRouteParams());
    }

    /**
     * @param Customer $customer
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm(Customer $customer)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_customer_delete', ['id' => $customer->getId()]))
            ->setMethod('DELETE')
            ->setData($customer)
            ->getForm();
    }
}