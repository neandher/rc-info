<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HomepageController extends Controller
{
    /**
     * @Route("/", name="site_homepage")
     */
    public function indexAction()
    {
        return $this->render('site/homepage/index.html.twig');
    }
}
