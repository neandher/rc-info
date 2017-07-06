<?php

namespace AdminBundle\EventListener;

use AdminBundle\Bill\Remessa;
use AdminBundle\Entity\Bill;
use AdminBundle\Entity\BillRemessa;
use AdminBundle\Event\BillEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class BillRemessaGenerate implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var Remessa
     */
    private $remessa;

    /**
     * BillRemessaGenerate constructor.
     * @param EntityManagerInterface $em
     * @param Remessa $remessa
     */
    public function __construct(EntityManagerInterface $em, Remessa $remessa)
    {
        $this->em = $em;
        $this->remessa = $remessa;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            BillEvents::CREATE_SUCCESS => 'onCreateSuccess',
            BillEvents::CREATE_COMPLETED => 'save',
            BillEvents::UPDATE_COMPLETED => 'save',
        ];
    }

    public function onCreateSuccess(GenericEvent $event)
    {
        /** @var Bill $bill */
        $bill = $event->getSubject();

        $billRemessa = new BillRemessa();
        $billRemessa
            ->setDescription('Remessa de Fatura Avulsa')
            ->setSent(false)
            ->addBill($bill);
    }

    public function save(GenericEvent $event)
    {
        /** @var BillRemessa $billRemessa */
        $billRemessa = $event->getSubject();

        if ($billRemessa->getSent()) {
            return;
        }

        $this->remessa->save($billRemessa->getBills(), $billRemessa->getId());
    }
}