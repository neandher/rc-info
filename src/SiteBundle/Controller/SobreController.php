<?php

namespace SiteBundle\Controller;

use SiteBundle\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SobreController extends Controller
{
    /**
     * @Route("/sobre", name="site_sobre")
     */
    public function indexAction()
    {
        $customersTestimonials = $this->getDoctrine()->getRepository(Customer::class)->findAllCms([
            'text' => true,
            'limit' => 10
        ]);

        $customersLogos = $this->getDoctrine()->getRepository(Customer::class)->findAllCms([
            'logo' => true,
            'limit' => 30
        ]);

        return $this->render('site/sobre/index.html.twig', [
            'customersTestimonials' => $customersTestimonials,
            'customersLogos' => $customersLogos
        ]);
    }
}
