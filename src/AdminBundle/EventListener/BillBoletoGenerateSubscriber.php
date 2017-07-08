<?php

namespace AdminBundle\EventListener;

use AdminBundle\Bill\Boleto;
use AdminBundle\Entity\BillRemessa;
use AdminBundle\Event\BillEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class BillBoletoGenerateSubscriber implements EventSubscriberInterface
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
            BillEvents::UPDATE_COMPLETED => 'generate',
        ];
    }

    public function generate(GenericEvent $event)
    {
        /** @var BillRemessa $billRemessa */
        $billRemessa = $event->getSubject();

        if ($billRemessa->getSent()) {
            return;
        }

        $company = $event->getArgument('company');

        if (!$company) {
            return;
        }

        foreach ($billRemessa->getBills() as $bill) {
            $this->boleto->renderPdf($bill, $company, true);
        }
    }
}