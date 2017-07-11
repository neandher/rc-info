<?php

namespace AdminBundle\EventListener;

use AdminBundle\Bill\Boleto;
use AdminBundle\Entity\Bill;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Filesystem\Filesystem;

class BillBoletoRemoveSubscriber implements EventSubscriber
{
    /**
     * @var Boleto
     */
    private $boleto;

    /**
     * BillBoletoRemoveSubscriber constructor.
     * @param Boleto $boleto
     */
    public function __construct(Boleto $boleto)
    {
        $this->boleto = $boleto;
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return array(
            'preRemove',
        );
    }

    public function preRemove(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if (!($entity instanceof Bill)) {
            return;
        }

        $file = $this->boleto->getBoletoFilePath() . '/' . $this->boleto->getBoletoFileName($entity);

        $fs = new Filesystem();
        $fs->remove($file);
    }

}