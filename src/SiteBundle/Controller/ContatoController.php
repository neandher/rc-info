<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ContatoController extends Controller
{
    /**
     * @Route("/contato", name="site_contato")
     */
    public function indexAction()
    {
        return $this->render('site/contato/index.html.twig');
    }
}
