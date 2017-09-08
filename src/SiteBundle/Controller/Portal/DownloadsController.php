<?php

namespace SiteBundle\Controller\Portal;

use AdminBundle\Entity\Bill;
use AdminBundle\Entity\Downloads;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DownloadsController
 * @package SiteBundle\Controller\Account
 *
 * @Route("/downloads")
 */
class DownloadsController extends Controller
{
    /**
     * @Route("/", name="site_portal_downloads")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Downloads::class);

        $downloads = $this->getDoctrine()->getRepository(Downloads::class)->findLatestPortal($pagination);

        return $this->render('site/portal/downloads/index.html.twig', [
            'downloads' => $downloads
        ]);
    }

    /**
     * @Route("/{id}/downloadFile", name="site_portal_downloads_download")
     *
     * @param Downloads $downloads
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadFile(Downloads $downloads)
    {
        return $this->get('app.admin.download_file')->download($downloads);
    }
}