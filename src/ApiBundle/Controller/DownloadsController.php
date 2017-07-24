<?php

namespace ApiBundle\Controller;

use AdminBundle\Entity\Downloads;
use AdminBundle\Form\Type\DownloadsType;
use ApiBundle\Util\Helpers;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DownloadsController
 * @package ApiBundle\Controller\Api
 *
 * @Route("/downloads")
 * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
 */
class DownloadsController extends BaseController
{
    /**
     * @Route("/new", name="api_downloads_new")
     * @Method("POST")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return Helpers::json([
                'status' => 'error',
                'code' => 404,
                'msg' => 'Acesso inválido'
            ]);
        }

        $download = new Downloads();
        $form = $this->createForm(DownloadsType::class, $download, ['csrf_protection' => false]);

        //$form->submit($request->files->all());

        $data = array_merge(
            $request->request->all(),
            $request->files->all()
        );

        $form->submit($data);

        if (!$form->isValid()) {
            return Helpers::json([
                'status' => 'error',
                'code' => 400,
                'errors' => Helpers::getErrorsFromForm($form)
            ]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($download);
        $em->flush();

        return $this->createApiResponse($download);
    }

//    /**
//     * @Route("/{id}/upload", name="api_downloads_upload", requirements={"id": "\d+"})
//     * @Method("POST")
//     * @param Request $request
//     * @param $id
//     * @return \Symfony\Component\HttpFoundation\Response
//     */
//    public function uploadAction(Request $request, $id)
//    {
//        if (!$request->isXmlHttpRequest()) {
//            return Helpers::json([
//                'status' => 'error',
//                'code' => 404,
//                'msg' => 'Acesso inválido'
//            ]);
//        }
//
//        $download = $this->getDoctrine()->getRepository(Downloads::class)->findOneBy(['id' => $id]);
//
//        if (!$download) {
//            return Helpers::json([
//                'status' => 'error',
//                'code' => 404,
//                'msg' => 'Download não encontrado.'
//            ]);
//        }
//
//        $form = $this->createForm(DownloadsFileType::class, $download, ['csrf_protection' => false]);
//        $form->submit($request->files->all());
//
//        if (!$form->isValid()) {
//            return Helpers::json([
//                'status' => 'error',
//                'code' => 400,
//                'errors' => Helpers::getErrorsFromForm($form)
//            ]);
//        }
//
//        $em = $this->getDoctrine()->getManager();
//        $em->persist($download);
//        $em->flush();
//
//        return Helpers::json([
//            'status' => 'success',
//            'code' => 200,
//            'data' => $download
//        ]);
//    }
}