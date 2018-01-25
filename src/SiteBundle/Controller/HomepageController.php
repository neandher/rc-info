<?php

namespace SiteBundle\Controller;

use SiteBundle\Entity\Banner;
use SiteBundle\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HomepageController extends Controller
{
    /**
     * @Route("/", name="site_homepage")
     */
    public function indexAction()
    {
        $customers = $this->getDoctrine()->getRepository(Customer::class)->findAllCms([
            'logo' => true,
            'limit' => 10
        ]);

        $banners = $this->getDoctrine()->getRepository(Banner::class)->findLatestCms();

        return $this->render('site/homepage/index.html.twig', [
            'customers' => $customers,
            'banners' => $banners
        ]);
    }
}
