<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DownloadsController extends Controller
{
    /**
     * @Route("/downloads", name="site_downloads")
     */
    public function indexAction()
    {
        return $this->render('site/downloads/index.html.twig');
    }
}
