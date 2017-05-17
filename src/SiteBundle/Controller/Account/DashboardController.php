<?php

namespace SiteBundle\Controller\Account;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DashboardController
 * @package SiteBundle\Controller\Account
 *
 * @Route("/dashboard")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/", name="site_account_dashboard")
     */
    public function indexAction()
    {
        return $this->render('site/account/dashboard.html.twig');
    }
}