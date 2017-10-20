<?php

namespace SiteBundle\Controller\Portal;

use AdminBundle\Entity\Bill;
use AdminBundle\Entity\Video;
use AdminBundle\Entity\VideoCategory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TreinamentosController
 * @package SiteBundle\Controller\Portal
 *
 * @Route("/treinamentos")
 */
class TreinamentosController extends Controller
{

    /*public function videosAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Video::class);

        $videos = $this->getDoctrine()->getRepository(Video::class)->findLatestPortal($pagination);

        return $this->render('site/portal/treinamentos/index.html.twig', [
            'videos' => $videos
        ]);
    }*/

    /**
     * @Route("/", name="site_portal_treinamentos")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, VideoCategory::class);

        $categories = $this->getDoctrine()->getRepository(VideoCategory::class)->findLatestPortal($pagination);

        return $this->render('site/portal/treinamentos/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/videos", name="site_portal_treinamentos_videos")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function videosAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Video::class);

        $routeParams = $pagination->getRouteParams();

        if(empty($routeParams['cat'])){
            $this->redirectToRoute('site_portal_treinamentos');
        }

        $videos = $this->getDoctrine()->getRepository(Video::class)->findLatestPortal($pagination);

        $category = '';

        /** @var Video $video */
        foreach ($videos as $video){
            $category = $video->getCategory()->getDescription();
        }

        return $this->render('site/portal/treinamentos/videos.html.twig', [
            'videos' => $videos,
            'category' => $category
        ]);
    }

    /**
     * @Route("/{id}/video", name="site_portal_treinamentos_video")
     *
     * @param Video $video
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function videoFile(Video $video)
    {
        return $this->render('site/portal/treinamentos/show.html.twig', [
            'video' => $video
        ]);
    }
}