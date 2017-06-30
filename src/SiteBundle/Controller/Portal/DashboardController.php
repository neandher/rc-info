<?php

namespace SiteBundle\Controller\Portal;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DashboardController
 * @package SiteBundle\Controller\Account
 *
 */
class DashboardController extends Controller
{
    /**
     * @Route("/", name="site_portal")
     * @Route("/dashboard", name="site_portal_dashboard")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('site_portal_faturas');
    }
}