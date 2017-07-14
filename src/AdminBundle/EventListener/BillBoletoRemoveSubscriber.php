<?php

namespace AdminBundle\EventListener;

use AdminBundle\Bill\Boleto;
use AdminBundle\Entity\Bill;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Knp\Bundle\GaufretteBundle\FilesystemMap;

class BillBoletoRemoveSubscriber implements EventSubscriber
{
    /**
     * @var Boleto
     */
    private $boleto;
    /**
     * @var FilesystemMap
     */
    private $fs;

    /**
     * BillBoletoRemoveSubscriber constructor.
     * @param Boleto $boleto
     * @param FilesystemMap $fs
     */
    public function __construct(Boleto $boleto, FilesystemMap $fs)
    {
        $this->boleto = $boleto;
        $this->fs = $fs->get('boletos_fs');
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
        
        if($this->fs->has('/' . $this->boleto->getBoletoFileName($entity))){
            $this->fs->delete('/' . $this->boleto->getBoletoFileName($entity));
        }
    }

}