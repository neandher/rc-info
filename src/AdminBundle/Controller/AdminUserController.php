<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\Type\AdminUserType;
use AdminBundle\Entity\AdminUser;
use AdminBundle\Model\AdminUserInterface;
use AppBundle\Event\FlashBagEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Event\UserEvents;

/**
 * Class AdminUserController
 * @package AppBundle\Controller\Admin
 *
 * @Route("/user")
 */
class AdminUserController extends BaseController
{
    /**
     * @Route("/", name="admin_user_index")
     * @Method({"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, AdminUser::class);

        $adminUsers = $this->getDoctrine()->getRepository(AdminUser::class)->findLatest($pagination);

        $deleteForms = [];
        foreach ($adminUsers as $user) {
            $deleteForms[$user->getId()] = $this->createDeleteForm($user)->createView();
        }

        return $this->render('admin/user/index.html.twig', [
            'adminUsers' => $adminUsers,
            'pagination' => $pagination,
            'delete_forms' => $deleteForms
        ]);
    }

    /**
     * @Route("/new", name="admin_user_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, AdminUser::class);

        $adminUser = new AdminUser();

        $form = $this->createForm(AdminUserType::class, $adminUser);
        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->get('event_dispatcher')->dispatch(UserEvents::REGISTRATION_SUCCESS, new GenericEvent($adminUser));

            $this->handleSuperAdmin($form, $adminUser);

            $em = $this->getDoctrine()->getManager();
            $em->persist($adminUser);
            $em->flush();

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_INSERTED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_user_new',
                'admin_user_edit',
                ['id' => $adminUser->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'adminUser' => $adminUser,
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/edit", requirements={"id" : "\d+"}, name="admin_user_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param AdminUser $adminUser
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(AdminUser $adminUser, Request $request)
    {
        $this->handleAuthorization($adminUser);

        $pagination = $this->get('app.util.pagination')->handle($request, AdminUser::class);

        $form = $this->createForm(AdminUserType::class, $adminUser, [
            'is_edit' => true,
            'validation_groups' => []
        ]);

        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->get('event_dispatcher')->dispatch(UserEvents::REGISTRATION_SUCCESS, new GenericEvent($adminUser));

            $this->handleSuperAdmin($form, $adminUser);

            $em = $this->getDoctrine()->getManager();
            $em->persist($adminUser);
            $em->flush();

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_UPDATED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_user_new',
                'admin_user_edit',
                ['id' => $adminUser->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'adminUser' => $adminUser,
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/delete", requirements={"id" : "\d+"}, name="admin_user_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param AdminUser $adminUser
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletAction(Request $request, AdminUser $adminUser)
    {
        $this->handleAuthorization($adminUser);
        
        $pagination = $this->get('app.util.pagination')->handle($request, AdminUser::class);

        $form = $this->createDeleteForm($adminUser);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($adminUser);
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

        return $this->redirectToRoute('admin_user_index', $pagination->getRouteParams());
    }

    /**
     * @param AdminUser $adminUser
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm(AdminUser $adminUser)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_user_delete', ['id' => $adminUser->getId()]))
            ->setMethod('DELETE')
            ->setData($adminUser)
            ->getForm();
    }

    private function handleSuperAdmin(FormInterface $form, AdminUserInterface $adminUser)
    {
        $data = $form->all();

        if ($data['isSuperAdmin']->getData()) {
            $adminUser->setSuperAdmin(true);
        } else {
            $adminUser->setSuperAdmin(false);
        }
    }

    private function handleAuthorization($adminUser)
    {
        if ($this->getUser() !== $adminUser) {
            $this->denyAccessUnlessGranted(
                AdminUserInterface::SUPER_ADMIN_ROLE,
                null,
                'Somente usuários Super Admin podem alterar dados de outros usuários!'
            );
        }
    }
}