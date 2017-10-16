<?php

namespace SiteBundle\Controller;

use AppBundle\Event\FlashBagEvents;
use SiteBundle\Form\ExperimenteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ExperimenteController extends Controller
{
    /**
     * @Route("/experimente", name="site_experimente")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        return $this->redirectToRoute('site_homepage');

        /*$form = $this->createForm(ExperimenteType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $textBody = 'Nome: ' . $data['name'] . ' 
Email: ' . $data['email'] . ' 
Tel: ' . $data['tel'] . ' 
Mensagem: ' . $data['message'] . '';

            $message = \Swift_Message::newInstance()
                ->setSubject('Nova solicitação feita pelo site')
                ->setFrom($this->getParameter('email_sender'))
                ->setTo($this->getParameter('email_contact_to'))
                ->setBody($textBody, 'text/plain');

            $this->get('swiftmailer.mailer')->send($message);

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                'Sua solicitação foi enviada com sucesso!'
            );

            return $this->redirectToRoute('site_experimente');
        }

        return $this->render('site/experimente/index.html.twig', [
            'form' => $form->createView()
        ]);*/
    }
}
