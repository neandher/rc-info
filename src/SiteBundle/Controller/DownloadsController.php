<?php

namespace SiteBundle\Controller;

use AdminBundle\Entity\CMSDownloads;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DownloadsController extends Controller
{
    /**
     * @Route("/downloads", name="site_downloads")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, CMSDownloads::class);

        $downloads = $this->getDoctrine()->getRepository(CMSDownloads::class)->findLatestSite($pagination);

        return $this->render('site/downloads/index.html.twig', [
            'downloads' => $downloads
        ]);
    }
}
