<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\VideoCategory;
use AdminBundle\Form\Type\VideoCategoryType;
use AppBundle\Event\FlashBagEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class VideoCategoryController
 * @package AppBundle\Controller\Admin
 *
 * @Route("/video-category")
 */
class VideoCategoryController extends BaseController
{
    /**
     * @Route("/", name="admin_video_category_index")
     * @Method({"GET"})
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, VideoCategory::class);

        $videoCategories = $this->getDoctrine()->getRepository(VideoCategory::class)->findLatest($pagination);

        $deleteForms = [];
        foreach ($videoCategories as $videoCategory) {
            $deleteForms[$videoCategory[0]->getId()] = $this->createDeleteForm($videoCategory[0])->createView();
        }

        return $this->render(
            'admin/videoCategory/index.html.twig',
            [
                'videoCategories'       => $videoCategories,
                'pagination'   => $pagination,
                'delete_forms' => $deleteForms,
            ]
        );
    }

    /**
     * @Route("/new", name="admin_video_category_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, VideoCategory::class);

        $videoCategory = new VideoCategory();

        $form = $this->createForm(VideoCategoryType::class, $videoCategory);
        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($videoCategory);
            $em->flush();

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_INSERTED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_video_category_new',
                'admin_video_category_edit',
                ['id' => $videoCategory->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_video_category_index');
        }

        return $this->render(
            'admin/videoCategory/new.html.twig',
            [
                'form'       => $form->createView(),
                'pagination' => $pagination,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", requirements={"id" : "\d+"}, name="admin_video_category_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param VideoCategory $videoCategory
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(VideoCategory $videoCategory, Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, VideoCategory::class);

        $form = $this->createForm(VideoCategoryType::class, $videoCategory);

        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($videoCategory);
            $em->flush();

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_UPDATED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_video_category_new',
                'admin_video_category_edit',
                ['id' => $videoCategory->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_video_category_index');
        }

        return $this->render(
            'admin/videoCategory/edit.html.twig',
            [
                'videoCategory'      => $videoCategory,
                'form'       => $form->createView(),
                'pagination' => $pagination,
            ]
        );
    }

    /**
     * @Route("/{id}/delete", requirements={"id" : "\d+"}, name="admin_video_category_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param VideoCategory $videoCategory
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletAction(Request $request, VideoCategory $videoCategory)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, VideoCategory::class);

        $form = $this->createDeleteForm($videoCategory);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($videoCategory);
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

        return $this->redirectToRoute('admin_video_category_index', $pagination->getRouteParams());
    }

    /**
     * @param VideoCategory $videoCategory
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm(VideoCategory $videoCategory)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('admin_video_category_delete', ['id' => $videoCategory->getId()]))
                    ->setMethod('DELETE')
                    ->setData($videoCategory)
                    ->getForm();
    }

    /**
     * @Route("/{id}/enable/", requirements={"id" : "\d+"}, name="admin_video_category_enable")
     *
     * @param VideoCategory $videoCategory
     * @param Request $request
     *
     * @internal param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function enable(Request $request, VideoCategory $videoCategory)
    {
        if ($videoCategory->getIsEnabled()) {
            $videoCategory->setIsEnabled(false);
        } else {
            $videoCategory->setIsEnabled(true);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($videoCategory);
        $em->flush();

        $pagination = $this->get('app.util.pagination')->handle($request, VideoCategory::class);

        return $this->redirectToRoute('admin_video_category_index', $pagination->getRouteParams());
    }
}