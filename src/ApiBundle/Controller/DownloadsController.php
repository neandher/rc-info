<?php

namespace ApiBundle\Controller;

use AdminBundle\Entity\Downloads;
use AdminBundle\Form\Type\DownloadsType;
use ApiBundle\Util\Helpers;
use Gedmo\Sluggable\Util\Urlizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DownloadsController
 * @package ApiBundle\Controller\Api
 *
 * @Route("/downloads")
 *
 *
 * ADICIONAR FIREWALL DE SEGURANÇA A ROTAS DA API
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
            return $this->createApiResponse([
                'status' => 'error',
                'code' => 404,
                'msg' => 'Acesso inválido'
            ]);
        }

        $download = new Downloads();
        $form = $this->createForm(DownloadsType::class, $download, ['csrf_protection' => false]);

        $em = $this->getDoctrine()->getManager();
        
        /** @var UploadedFile[] $file */
        foreach ($request->files->all() as $file) {

            $description = str_replace("." . $file[0]->getClientOriginalExtension(), "", $file[0]->getClientOriginalName());

            $request->request->set('description', $description);
            $request->request->set('publishedAt', (new \DateTime())->format('d-m-Y H:i'));
            $request->request->set('isEnabled', true);

            $data = array_merge(
                $request->request->all(),
                ['downloadFile' => $file[0]]
            );

            $form->submit($data);

            if (!$form->isValid()) {
                return $this->createApiResponse(['files' => [
                    [
                        'name' => $file[0]->getClientOriginalName(),
                        'size' => $file[0]->getSize(),
                        'error' => Helpers::getErrorsFromForm($form, 'str')
                    ]
                ]]);
            }
            
            $em->persist($download);
        }

        $em->flush();

        $return = ['files' => [
            [
                'name' => $download->getDownloadName(),
                //'url' => $request->getUriForPath($this->get('vich_uploader.templating.helper.uploader_helper')->asset($download, 'downloadFile')),
                'url' => $this->generateUrl('admin_downloads_download_file', ['id' => $download->getId()]),
                'size' => $download->getDownloadFile()->getSize(),
            ]
        ]];

        return $this->createApiResponse($return);
    }
}