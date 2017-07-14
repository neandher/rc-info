<?php

namespace AdminBundle\EventListener;

use AdminBundle\Bill\Remessa;
use AdminBundle\Entity\BillRemessa;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Knp\Bundle\GaufretteBundle\FilesystemMap;
use Symfony\Component\Filesystem\Filesystem;

class BillRemessaRemoveSubscriber implements EventSubscriber
{
    /**
     * @var Remessa
     */
    private $remessa;
    
    /**
     * @var FilesystemMap
     */
    private $fs;

    /**
     * BillRemessaRemoveSubscriber constructor.
     * @param Remessa $remessa
     * @param FilesystemMap $fs
     */
    public function __construct(Remessa $remessa, FilesystemMap $fs)
    {
        $this->remessa = $remessa;
        $this->fs = $fs->get('remessas_fs');
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

        if (!($entity instanceof BillRemessa)) {
            return;
        }

        if($this->fs->has('/' . $this->remessa->getRemessaFileName($entity))){
            $this->fs->delete('/' . $this->remessa->getRemessaFileName($entity));
        }
    }

}