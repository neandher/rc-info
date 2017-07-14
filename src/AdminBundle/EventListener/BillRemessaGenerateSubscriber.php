<?php

namespace AdminBundle\EventListener;

use AdminBundle\Bill\Remessa;
use AdminBundle\Entity\Bill;
use AdminBundle\Entity\BillRemessa;
use AdminBundle\Event\BillEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class BillRemessaGenerateSubscriber implements EventSubscriberInterface
{
    /**
     * @var Remessa
     */
    private $remessa;

    /**
     * BillRemessaGenerate constructor.
     * @param Remessa $remessa
     */
    public function __construct(Remessa $remessa)
    {
        $this->remessa = $remessa;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            BillEvents::CREATE_SUCCESS => 'onCreateSuccess',
            BillEvents::CREATE_COMPLETED => 'generate',
            BillEvents::UPDATE_COMPLETED => 'generate',
            BillEvents::DELETE_COMPLETED => 'generate',
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

    public function generate(GenericEvent $event)
    {
        /** @var BillRemessa $billRemessa */
        $billRemessa = $event->getSubject();

        if ($billRemessa->getSent()) {
            return;
        }
        
        $company = $event->getArgument('company');
        
        $this->remessa->renderRem($billRemessa, $company);
    }
}