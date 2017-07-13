<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Downloads;
use AdminBundle\Form\Type\DownloadsType;
use AppBundle\Event\FlashBagEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DownloadsController
 * @package AppBundle\Controller\Admin
 *
 * @Route("/downloads")
 */
class DownloadsController extends BaseController
{
    /**
     * @Route("/", name="admin_downloads_index")
     * @Method({"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Downloads::class);

        $downloadss = $this->getDoctrine()->getRepository(Downloads::class)->findLatest($pagination);

        $deleteForms = [];
        foreach ($downloadss as $downloads) {
            $deleteForms[$downloads->getId()] = $this->createDeleteForm($downloads)->createView();
        }

        return $this->render('admin/downloads/index.html.twig', [
            'downloadss' => $downloadss,
            'pagination' => $pagination,
            'delete_forms' => $deleteForms
        ]);
    }

    /**
     * @Route("/new", name="admin_downloads_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Downloads::class);

        $downloads = new Downloads();

        $form = $this->createForm(DownloadsType::class, $downloads);
        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($downloads);
            $em->flush();

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_INSERTED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_downloads_new',
                'admin_downloads_edit',
                ['id' => $downloads->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_downloads_index');
        }

        return $this->render('admin/downloads/new.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/edit", requirements={"id" : "\d+"}, name="admin_downloads_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Downloads $downloads
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Downloads $downloads, Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Downloads::class);

        $form = $this->createForm(DownloadsType::class, $downloads, [
            'validation_groups' => ['Default']
        ]);

        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($downloads);
            $em->flush();

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_UPDATED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_downloads_new',
                'admin_downloads_edit',
                ['id' => $downloads->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_downloads_index');
        }

        return $this->render('admin/downloads/edit.html.twig', [
            'downloads' => $downloads,
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/delete", requirements={"id" : "\d+"}, name="admin_downloads_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Downloads $downloads
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletAction(Request $request, Downloads $downloads)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Downloads::class);

        $form = $this->createDeleteForm($downloads);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($downloads);
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

        return $this->redirectToRoute('admin_downloads_index', $pagination->getRouteParams());
    }

    /**
     * @param Downloads $downloads
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm(Downloads $downloads)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_downloads_delete', ['id' => $downloads->getId()]))
            ->setMethod('DELETE')
            ->setData($downloads)
            ->getForm();
    }

    /**
     * @Route("/{id}/downloadFile", name="admin_downloads_download_file")
     *
     * @param Downloads $downloads
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadFile(Downloads $downloads)
    {
        return $this->get('app.admin.download_file')->download($downloads);
    }
}