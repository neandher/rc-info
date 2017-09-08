<?php

namespace SiteBundle\Controller\Portal;

use AdminBundle\Entity\Bill;
use AppBundle\Event\FlashBagEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FaturasController
 * @package SiteBundle\Controller\Account
 *
 * @Route("/faturas")
 */
class FaturasController extends Controller
{
    /**
     * @Route("/", name="site_portal_faturas")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Bill::class);;

        $bills = $this->getDoctrine()->getRepository(Bill::class)->findLatestPortal($pagination);

        return $this->render('site/portal/faturas/index.html.twig', [
            'bills' => $bills
        ]);
    }

    /**
     * @Route("/{id}/boleto", requirements={"id" : "\d+"}, name="site_portal_faturas_boleto")
     * @param Bill $bill
     * @return Response
     */
    public function getBoleto(Bill $bill)
    {
        $response = $this->get('app.admin.bill_boleto')->download($bill);

        if (!$response) {

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_ERROR,
                'Houve um erro ao baixar o arquivo.'
            );

            return $this->redirectToRoute('site_portal_faturas');
        }

        return $response;
    }
}