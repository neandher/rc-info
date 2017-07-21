<?php

namespace AdminBundle\EventListener;

use AdminBundle\Entity\Bill;
use AdminBundle\Event\BillBoletoEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class BillBoletoNameSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * BillBoletoNameSubscriber constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            BillBoletoEvents::GENERATE_SUCCESS => 'onGenerateSuccess'
        ];
    }

    public function onGenerateSuccess(GenericEvent $event)
    {
        /** @var Bill $bill */
        $bill = $event->getSubject();

        $bill->setBoletoName($event->getArgument('boletoName'));

        $this->em->persist($bill);
        $this->em->flush();
    }

}