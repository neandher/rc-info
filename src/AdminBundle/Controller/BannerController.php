<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\Type\BannerType;
use AppBundle\Event\FlashBagEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SiteBundle\Entity\Banner;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Event\UserEvents;

/**
 * Class BannerController
 * @package AppBundle\Controller\Admin
 *
 * @Route("/banner")
 */
class BannerController extends BaseController
{
    /**
     * @Route("/", name="banner_index")
     * @Method({"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Banner::class);

        $banners = $this->getDoctrine()->getRepository(Banner::class)->findLatest($pagination);

        $deleteForms = [];
        foreach ($banners as $banner) {
            $deleteForms[$banner->getId()] = $this->createDeleteForm($banner)->createView();
        }

        return $this->render('admin/banner/index.html.twig', [
            'banners' => $banners,
            'pagination' => $pagination,
            'delete_forms' => $deleteForms
        ]);
    }

    /**
     * @Route("/new", name="banner_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Banner::class);

        $banner = new Banner();

        $form = $this->createForm(BannerType::class, $banner);
        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($banner);
            $em->flush();

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_INSERTED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'banner_new',
                'banner_edit',
                ['id' => $banner->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('banner_index');
        }

        return $this->render('admin/banner/new.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/edit", requirements={"id" : "\d+"}, name="banner_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Banner $banner
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Banner $banner, Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Banner::class);

        $form = $this->createForm(BannerType::class, $banner);

        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($banner);
            $em->flush();

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_UPDATED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'banner_new',
                'banner_edit',
                ['id' => $banner->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('banner_index');
        }

        return $this->render('admin/banner/edit.html.twig', [
            'banner' => $banner,
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/delete", requirements={"id" : "\d+"}, name="banner_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Banner $banner
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletAction(Request $request, Banner $banner)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Banner::class);

        $form = $this->createDeleteForm($banner);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($banner);
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

        return $this->redirectToRoute('banner_index', $pagination->getRouteParams());
    }

    /**
     * @param Banner $banner
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm(Banner $banner)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('banner_delete', ['id' => $banner->getId()]))
            ->setMethod('DELETE')
            ->setData($banner)
            ->getForm();
    }
}