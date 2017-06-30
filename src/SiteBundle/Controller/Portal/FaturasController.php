<?php

namespace SiteBundle\Controller\Portal;

use AdminBundle\Entity\Bill;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FaturasController
 * @package SiteBundle\Controller\Account
 *
 */
class FaturasController extends Controller
{
    /**
     * @Route("/faturas", name="site_portal_faturas")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $request->query->add(['sorting' => ['dueDateAt' => 'desc']]);
        $request->query->add(['num_items' => 6]);

        $pagination = $this->get('app.util.pagination')->handle($request, Bill::class);;

        $bills = $this->getDoctrine()->getRepository(Bill::class)->findLatest($pagination);

        return $this->render('site/portal/faturas/index.html.twig', [
            'bills' => $bills
        ]);
    }
}