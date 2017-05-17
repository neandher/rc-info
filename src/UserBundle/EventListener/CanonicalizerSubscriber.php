<?php

namespace UserBundle\EventListener;

use ShopBundle\Entity\Customer;
use UserBundle\Model\UserInterface;
use AppBundle\Util\Canonicalizer;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class CanonicalizerSubscriber implements EventSubscriber
{

    /**
     * @var Canonicalizer
     */
    private $canonicalizer;

    public function __construct(Canonicalizer $canonicalizer)
    {
        $this->canonicalizer = $canonicalizer;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate'
        );
    }

    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $this->canonicalize($eventArgs);
    }

    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        $this->canonicalize($eventArgs);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function canonicalize(LifecycleEventArgs $event)
    {
        $item = $event->getEntity();

        if ($item instanceof Customer) {
            $item->setEmailCanonical($this->canonicalizer->canonicalize($item->getEmail()));
        } elseif ($item instanceof UserInterface) {
            $item->setUsernameCanonical($this->canonicalizer->canonicalize($item->getUsername()));
            $item->setEmailCanonical($this->canonicalizer->canonicalize($item->getEmail()));
        }
    }
}