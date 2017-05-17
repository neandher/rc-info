<?php

namespace UserBundle\EventListener;

use AppBundle\Event\FlashBagEvents;
use UserBundle\Event\UserEvents;
use AppBundle\Util\FlashBag;
use UserBundle\Mailer\UserMailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class ResettingRequestSubscriber implements EventSubscriberInterface
{

    /**
     * @var UserMailer
     */
    private $mailer;

    /**
     * @var FlashBag
     */
    private $flashBagHelper;

    public function __construct(
        UserMailer $mailer,
        FlashBag $flashBagHelper
    )
    {
        $this->mailer = $mailer;
        $this->flashBagHelper = $flashBagHelper;
    }

    /**
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            UserEvents::RESETTING_REQUEST_SUCCESS => 'onResettingRequestSuccess'
        );
    }

    public function onResettingRequestSuccess(GenericEvent $event)
    {
        $user = $event->getSubject();
        $email_params = $event->getArgument('email_params');

        $this->mailer->sendLinkWithTokenEmailMessage($user, $email_params);

        $this->flashBagHelper->newMessage(
            FlashBagEvents::MESSAGE_TYPE_SUCCESS,
            'user.resetting.request.check_email',
            [
                'user_email' => $user->getObfuscatedEmail()
            ]

        );
    }

}