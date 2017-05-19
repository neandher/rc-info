<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SobreController extends Controller
{
    /**
     * @Route("/sobre", name="site_sobre")
     */
    public function indexAction()
    {
        return $this->render('site/sobre/index.html.twig');
    }
}
