<?php

namespace SiteBundle\Controller;

use AppBundle\Event\FlashBagEvents;
use SiteBundle\Form\ContatoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ContatoController extends Controller
{
    /**
     * @Route("/contato", name="site_contato")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(ContatoType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $textBody = 'Nome: ' . $data['name'] . ' 
Email: ' . $data['email'] . ' 
Telefone: ' . $data['tel'] . ' 
Mensagem: ' . $data['message'] . '';

            $message = \Swift_Message::newInstance()
                ->setSubject('Novo contato feito pelo site')
                ->setFrom($this->getParameter('email_sender'))
                ->setTo($this->getParameter('email_contact_to'))
                ->setBody($textBody, 'text/plain');

            $this->get('swiftmailer.mailer')->send($message);

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                'Sua mensagem foi enviada com sucesso!'
            );

            return $this->redirectToRoute('site_contato');
        }

        return $this->render('site/contato/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
