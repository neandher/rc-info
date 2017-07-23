<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Downloads;
use AdminBundle\Form\Type\DownloadsFileType;
use AdminBundle\Form\Type\DownloadsType;
use AdminBundle\Util\Helpers;
use AppBundle\Event\FlashBagEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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

        $downloads = $this->getDoctrine()->getRepository(Downloads::class)->findLatest($pagination);

        $deleteForms = [];
        foreach ($downloads as $download) {
            $deleteForms[$download->getId()] = $this->createDeleteForm($download)->createView();
        }

        return $this->render('admin/downloads/index.html.twig', [
            'downloads' => $downloads,
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

    /**
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     * @Route("/newAjax", name="admin_downloads_new_ajax")
     * @Method("POST")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAjaxAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return Helpers::json([
                'status' => 'error',
                'code' => 404,
                'msg' => 'Acesso inválido'
            ]);
        }

        $download = new Downloads();
        $form = $this->createForm(DownloadsType::class, $download, ['csrf_protection' => false]);

        $data = json_decode($request->getContent(), true);

        if($data == null){
            return Helpers::json([
                'status' => 'error',
                'code' => 404,
                'msg' => 'Json inválido.'
            ]);
        }

        $form->submit($data);

        if (!$form->isValid()) {
            return Helpers::json([
                'status' => 'error',
                'code' => 400,
                'errors' => Helpers::getErrorsFromForm($form)
            ]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($download);
        $em->flush();

        return Helpers::json([
            'status' => 'success',
            'code' => 200,
            'data' => $download
        ]);
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     * @Route("/{id}/uploadAjax", name="admin_downloads_upload_ajax", requirements={"id": "\d+"})
     * @Method("POST")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function uploadAjaxAction(Request $request, $id)
    {
        if (!$request->isXmlHttpRequest()) {
            return Helpers::json([
                'status' => 'error',
                'code' => 404,
                'msg' => 'Acesso inválido'
            ]);
        }

        $download = $this->getDoctrine()->getRepository(Downloads::class)->findOneBy(['id' => $id]);

        if (!$download) {
            return Helpers::json([
                'status' => 'error',
                'code' => 404,
                'msg' => 'Download não encontrado.'
            ]);
        }

        $form = $this->createForm(DownloadsFileType::class, $download, ['csrf_protection' => false]);
        $form->submit($request->files->all());

        if (!$form->isValid()) {
            return Helpers::json([
                'status' => 'error',
                'code' => 400,
                'errors' => Helpers::getErrorsFromForm($form)
            ]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($download);
        $em->flush();

        return Helpers::json([
            'status' => 'success',
            'code' => 200,
            'data' => $download
        ]);
    }
}