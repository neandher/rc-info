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

            $this->sendMessage(
                'site/contato/email.html.twig',
                $data,
                $this->getParameter('email_sender'),
                $this->getParameter('email_contact_to')
            );

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

//    /**
//     * @Route("/contato-template", name="site_contato_template")
//     * @param Request $request
//     * @return \Symfony\Component\HttpFoundation\Response
//     */
//    public function templateEmailAction(Request $request)
//    {
//        $data['name'] = '';
//        $data['email'] = '';
//        $data['tel'] = '';
//        $data['message'] = '';
//
//        return $this->render('site/contato/email.html.twig', [
//            'name' => 'Neandher',
//            'email' => 'neandher89@gmail.com',
//            'tel' => '27996279047',
//            'message' => 'Teste!!',
//        ]);
//    }

    /**
     * @param string $templateName
     * @param array $context
     * @param string $fromEmail
     * @param string $toEmail
     * @return int
     */
    protected function sendMessage($templateName, $context, $fromEmail, $toEmail)
    {
        $template = $this->get('twig')->load($templateName);
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);

        $htmlBody = '';

        if ($template->hasBlock('body_html', $context)) {
            $htmlBody = $template->renderBlock('body_html', $context);
        }

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail);

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        return $this->get('mailer')->send($message);
    }
}
