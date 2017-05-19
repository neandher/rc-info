<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ExperimenteController extends Controller
{
    /**
     * @Route("/experimente", name="site_experimente")
     */
    public function indexAction()
    {
        return $this->render('site/experimente/index.html.twig');
    }
}
