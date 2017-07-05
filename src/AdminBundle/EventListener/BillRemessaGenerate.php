<?php

namespace AdminBundle\EventListener;

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
     * BillRemessaGenerate constructor.
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
            BillEvents::CREATE_SUCCESS => 'onCreateSuccess',
            BillEvents::CREATE_COMPLETED => 'onCreateCompleted',
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

    public function onCreateCompleted(GenericEvent $event)
    {
        /** @var Bill $bill */
        $bill = $event->getSubject();

        //gerar o arquivo de remessa
    }
}