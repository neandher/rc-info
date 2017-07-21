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
     * @var FilesystemMap
     */
    private $fs;

    /**
     * BillBoletoRemoveSubscriber constructor.
     * @param FilesystemMap $fs
     */
    public function __construct(FilesystemMap $fs)
    {
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
        
        if($this->fs->has('/' . $entity->getBoletoName())){
            $this->fs->delete('/' . $entity->getBoletoName());
        }
    }

}