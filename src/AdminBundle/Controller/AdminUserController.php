<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\Type\AdminUserType;
use AppBundle\Controller\BaseController;
use AdminBundle\Entity\AdminUser;
use AppBundle\Event\FlashBagEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Event\UserEvents;

/**
 * Class AdminUserController
 * @package AppBundle\Controller\Admin
 *
 * @Route("/user")
 */
class AdminUserController extends Controller
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

        return $this->render('admin/user/index.html.twig', [
            'adminUsers' => $adminUsers,
            'pagination' => $pagination
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
        $adminUser = new AdminUser();

        $form = $this->createForm(AdminUserType::class, $adminUser);

        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {

            $this->get('event_dispatcher')->dispatch(UserEvents::REGISTRATION_SUCCESS, new GenericEvent($adminUser));

            $em = $this->getDoctrine()->getManager();
            $em->persist($adminUser);
            $em->flush();

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_INSERTED
            );

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", requirements={"id" : "\d+"}, name="admin_user_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param AdminUser $adminUser
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, AdminUser $adminUser)
    {

    }

    /**
     * @Route("/{id}/delete", requirements={"id" : "\d+"}, name="admin_user_delete")
     * @Method("DELETE")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletAction(Request $request)
    {

    }
}