<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\CMSDownloads;
use AdminBundle\Form\Type\CMSDownloadsType;
use AppBundle\Event\FlashBagEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CMSDownloadsController
 * @package AppBundle\Controller\Admin
 *
 * @Route("/cms-downloads")
 */
class CMSDownloadsController extends BaseController
{
    /**
     * @Route("/", name="admin_cms_downloads_index")
     * @Method({"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, CMSDownloads::class);

        $downloads = $this->getDoctrine()->getRepository(CMSDownloads::class)->findLatest($pagination);

        $deleteForms = [];
        foreach ($downloads as $download) {
            $deleteForms[$download->getId()] = $this->createDeleteForm($download)->createView();
        }

        return $this->render('admin/cms/downloads/index.html.twig', [
            'downloads' => $downloads,
            'pagination' => $pagination,
            'delete_forms' => $deleteForms
        ]);
    }

    /**
     * @Route("/new", name="admin_cms_downloads_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, CMSDownloads::class);

        $downloads = new CMSDownloads();

        $form = $this->createForm(CMSDownloadsType::class, $downloads);
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
                'admin_cms_downloads_new',
                'admin_cms_downloads_edit',
                ['id' => $downloads->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_cms_downloads_index');
        }

        return $this->render('admin/cms/downloads/new.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/edit", requirements={"id" : "\d+"}, name="admin_cms_downloads_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param CMSDownloads $downloads
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(CMSDownloads $downloads, Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, CMSDownloads::class);

        $form = $this->createForm(CMSDownloadsType::class, $downloads, [
            'validation_groups' => ['Default'],
            'is_edit' => true
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
                'admin_cms_downloads_new',
                'admin_cms_downloads_edit',
                ['id' => $downloads->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_cms_downloads_index');
        }

        return $this->render('admin/cms/downloads/edit.html.twig', [
            'downloads' => $downloads,
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/delete", requirements={"id" : "\d+"}, name="admin_cms_downloads_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param CMSDownloads $downloads
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletAction(Request $request, CMSDownloads $downloads)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, CMSDownloads::class);

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

        return $this->redirectToRoute('admin_cms_downloads_index', $pagination->getRouteParams());
    }

    /**
     * @param CMSDownloads $downloads
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm(CMSDownloads $downloads)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_cms_downloads_delete', ['id' => $downloads->getId()]))
            ->setMethod('DELETE')
            ->setData($downloads)
            ->getForm();
    }

    /**
     * @Route("/{id}/enable/", requirements={"id" : "\d+"}, name="admin_cms_downloads_enable")
     *
     * @param CMSDownloads $downloads
     * @param Request $request
     * @internal param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function enable(Request $request, CMSDownloads $downloads)
    {
        if ($downloads->getIsEnabled()) {
            $downloads->setIsEnabled(false);
        } else {
            $downloads->setIsEnabled(true);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($downloads);
        $em->flush();

        $pagination = $this->get('app.util.pagination')->handle($request, CMSDownloads::class);

        return $this->redirectToRoute('admin_cms_downloads_index', $pagination->getRouteParams());
    }
}