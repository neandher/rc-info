<?php

namespace UserBundle\Mailer;

use UserBundle\Model\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserMailer
{

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function __construct(
        \Swift_Mailer $mailer,
        UrlGeneratorInterface $router,
        \Twig_Environment $twig
    )
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->twig = $twig;
    }

//    /**
//     * @param UserInterface $user
//     * @param $params
//     * @return int
//     */
//    public function sendResettingEmailMessage(UserInterface $user, $params)
//    {
//        $url = $this->router->generate(
//            $params['route'],
//            array('token' => $user->getConfirmationToken()),
//            UrlGeneratorInterface::ABSOLUTE_URL
//        );
//
//        $context = ['user' => $user, 'url' => $url];
//
//        return $this->sendMessage($params['template'], $context, $params['from'], $user->getEmail());
//    }

    /**
     * @param UserInterface $user
     * @param $params
     * @return int
     */
    public function sendLinkWithTokenEmailMessage(UserInterface $user, $params)
    {
        $url = $this->router->generate(
            $params['route'],
            array('token' => $user->getConfirmationToken()),
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $context = ['user' => $user, 'url' => $url];

        return $this->sendMessage($params['template'], $context, $params['from'], $user->getEmail());
    }

    /**
     * @param string $templateName
     * @param array $context
     * @param string $fromEmail
     * @param string $toEmail
     * @return int
     */
    protected function sendMessage($templateName, $context, $fromEmail, $toEmail)
    {
        $context = $this->twig->mergeGlobals($context);
        $template = $this->twig->loadTemplate($templateName);
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

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

        return $this->mailer->send($message);
    }
}