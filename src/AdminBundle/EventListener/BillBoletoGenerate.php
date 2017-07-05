<?php

namespace AdminBundle\EventListener;

use AdminBundle\Bill\Boleto;
use AdminBundle\Entity\Bill;
use AdminBundle\Event\BillEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class BillBoletoGenerate implements EventSubscriberInterface
{
    /**
     * @var Boleto
     */
    private $boleto;

    /**
     * BillBoletoGenerate constructor.
     * @param Boleto $boleto
     */
    public function __construct(Boleto $boleto)
    {
        $this->boleto = $boleto;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            BillEvents::CREATE_COMPLETED => 'generate',
        ];
    }

    public function generate(GenericEvent $event)
    {
        /** @var Bill $bill */
        $bill = $event->getSubject();

        $this->boleto->renderPdf($bill, true);
    }
}