<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ProdutosController extends Controller
{
    /**
     * @Route("/produtos", name="site_produtos")
     */
    public function indexAction()
    {
        return $this->render('site/produtos/index.html.twig');
    }

    /**
     * @Route("/produtos2", name="site_produtos2")
     */
    public function index2Action()
    {
        return $this->render('site/produtos/index2.html.twig');
    }
}
