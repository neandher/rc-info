<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Video;
use AdminBundle\Form\Type\VideoType;
use AppBundle\Event\FlashBagEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class VideoController
 * @package AppBundle\Controller\Admin
 *
 * @Route("/video")
 */
class VideoController extends BaseController
{
    /**
     * @Route("/", name="admin_video_index")
     * @Method({"GET"})
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Video::class);

        $videos = $this->getDoctrine()->getRepository(Video::class)->findLatest($pagination);

        $deleteForms = [];
        foreach ($videos as $video) {
            $deleteForms[$video->getId()] = $this->createDeleteForm($video)->createView();
        }

        return $this->render(
            'admin/video/index.html.twig',
            [
                'videos'       => $videos,
                'pagination'   => $pagination,
                'delete_forms' => $deleteForms,
            ]
        );
    }

    /**
     * @Route("/new", name="admin_video_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Video::class);

        $video = new Video();

        $form = $this->createForm(VideoType::class, $video);
        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($video);
            $em->flush();

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_INSERTED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_video_new',
                'admin_video_edit',
                ['id' => $video->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_video_index');
        }

        return $this->render(
            'admin/video/new.html.twig',
            [
                'form'       => $form->createView(),
                'pagination' => $pagination,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", requirements={"id" : "\d+"}, name="admin_video_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Video $video
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Video $video, Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Video::class);

        $form = $this->createForm(VideoType::class, $video);

        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($video);
            $em->flush();

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_UPDATED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_video_new',
                'admin_video_edit',
                ['id' => $video->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_video_index');
        }

        return $this->render(
            'admin/video/edit.html.twig',
            [
                'video'      => $video,
                'form'       => $form->createView(),
                'pagination' => $pagination,
            ]
        );
    }

    /**
     * @Route("/{id}/delete", requirements={"id" : "\d+"}, name="admin_video_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Video $video
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletAction(Request $request, Video $video)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Video::class);

        $form = $this->createDeleteForm($video);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($video);
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

        return $this->redirectToRoute('admin_video_index', $pagination->getRouteParams());
    }

    /**
     * @param Video $video
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm(Video $video)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('admin_video_delete', ['id' => $video->getId()]))
                    ->setMethod('DELETE')
                    ->setData($video)
                    ->getForm();
    }

    /**
     * @Route("/{id}/enable/", requirements={"id" : "\d+"}, name="admin_video_enable")
     *
     * @param Video $video
     * @param Request $request
     *
     * @internal param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function enable(Request $request, Video $video)
    {
        if ($video->getIsEnabled()) {
            $video->setIsEnabled(false);
        } else {
            $video->setIsEnabled(true);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($video);
        $em->flush();

        $pagination = $this->get('app.util.pagination')->handle($request, Video::class);

        return $this->redirectToRoute('admin_video_index', $pagination->getRouteParams());
    }
}