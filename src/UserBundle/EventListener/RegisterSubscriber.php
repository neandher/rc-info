<?php

namespace UserBundle\EventListener;

use ShopBundle\Entity\Customer;
use UserBundle\Event\UserEvents;
use UserBundle\Mailer\UserMailer;
use AppBundle\Util\FlashBag;
use UserBundle\Model\UserInterface;
use UserBundle\Security\TokenGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegisterSubscriber implements EventSubscriberInterface
{

    /**
     * @var FlashBag
     */
    private $flashBag;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * @var UserMailer
     */
    private $mailer;

    /**
     * ResettingResetSubscriber constructor.
     *
     * @param FlashBag $flashBag
     * @param UrlGeneratorInterface $router
     * @param TokenGenerator $tokenGenerator
     * @param UserMailer $mailer
     */
    public function __construct(
        FlashBag $flashBag,
        UrlGeneratorInterface $router,
        TokenGenerator $tokenGenerator,
        UserMailer $mailer
    )
    {
        $this->flashBag = $flashBag;
        $this->router = $router;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            UserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        );
    }

    public function onRegistrationSuccess(GenericEvent $event)
    {
        /** @var UserInterface $user */
        $user = $event->getSubject();
        $needConfirmation = $event->hasArgument('need_confirmation') ? $event->getArgument('need_confirmation') : false;

        $user->setUsername($user->getEmail());

        if ($needConfirmation) {

            $user->setIsEnabled(false);

            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken($this->tokenGenerator->generateToken());
            }

            $email_params = $event->getArgument('email_params');

            $this->mailer->sendLinkWithTokenEmailMessage($user, $email_params);
        }
    }
}