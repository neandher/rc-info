<?php

namespace SiteBundle\Controller\Portal;

use AdminBundle\Entity\Bill;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DownloadsController
 * @package SiteBundle\Controller\Account
 *
 */
class DownloadsController extends Controller
{
    /**
     * @Route("/downloads", name="site_portal_downloads")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        return $this->render('site/portal/downloads/index.html.twig', []);
    }
}