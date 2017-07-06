<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\BillRemessa;
use AppBundle\Event\FlashBagEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RemessaController
 * @package AppBundle\Controller\Admin
 *
 * @Route("/remessa")
 */
class BillRemessaController extends BaseController
{
    /**
     * @Route("/", name="admin_bill_remessa_index")
     * @Method({"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, BillRemessa::class);

        $remessas = $this->getDoctrine()->getRepository(BillRemessa::class)->findLatest($pagination);

        $deleteForms = [];
        foreach ($remessas as $remessa) {
            $deleteForms[$remessa->getId()] = $this->createDeleteForm($remessa)->createView();
        }

        return $this->render('admin/remessa/index.html.twig', [
            'remessas' => $remessas,
            'pagination' => $pagination,
            'delete_forms' => $deleteForms
        ]);
    }

    /**
     * @Route("/{id}/delete", requirements={"id" : "\d+"}, name="admin_bill_remessa_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param BillRemessa $remessa
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletAction(Request $request, BillRemessa $remessa)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, BillRemessa::class);

        $form = $this->createDeleteForm($remessa);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($remessa);
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

        return $this->redirectToRoute('admin_bill_remessa_index', $pagination->getRouteParams());
    }

    /**
     * @param BillRemessa $remessa
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm(BillRemessa $remessa)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_bill_remessa_delete', ['id' => $remessa->getId()]))
            ->setMethod('DELETE')
            ->setData($remessa)
            ->getForm();
    }

    /**
     * @Route("/{id}/download/", requirements={"id" : "\d+"}, name="admin_bill_remessa_download")
     * @param Request $request
     * @param BillRemessa $billRemessa
     * @return Response
     */
    public function download(Request $request, BillRemessa $billRemessa)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, BillRemessa::class);
        $response = $this->get('app.admin.bill_remessa')->download($billRemessa);

        if (!$response) {

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_ERROR,
                'Houve um erro ao baixar o arquivo.'
            );

            return $this->redirectToRoute('admin_bill_remessa_index', $pagination->getRouteParams());
        }

        return $response;
    }

    /**
     * @Route("/{id}/sent/", requirements={"id" : "\d+"}, name="admin_bill_remessa_sent")
     * @param Request $request
     * @param BillRemessa $billRemessa
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function sentRemessa(Request $request, BillRemessa $billRemessa)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, BillRemessa::class);

        if ($request->query->has('sent')) {

            $sent = $request->query->get('sent');
            $billRemessa->setSent($sent);

            $em = $this->getDoctrine()->getManager();
            $em->persist($billRemessa);
            $em->flush();

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                'Remessa atualizada com sucesso.'
            );

            $request->query->remove('sent');
        } else {
            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_ERROR,
                'Não foi possível atualizar a remessa.'
            );
        }

        return $this->redirectToRoute('admin_bill_remessa_index', $pagination->getRouteParams());
    }
}