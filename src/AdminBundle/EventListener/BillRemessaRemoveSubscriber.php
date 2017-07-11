<?php

namespace AdminBundle\EventListener;

use AdminBundle\Bill\Remessa;
use AdminBundle\Entity\BillRemessa;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Filesystem\Filesystem;

class BillRemessaRemoveSubscriber implements EventSubscriber
{
    /**
     * @var Remessa
     */
    private $remessa;

    /**
     * BillRemessaRemoveSubscriber constructor.
     * @param Remessa $remessa
     */
    public function __construct(Remessa $remessa)
    {
        $this->remessa = $remessa;
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

        $file = $this->remessa->getRemessaFilePath() . '/' . $this->remessa->getRemessaFileName($entity);

        $fs = new Filesystem();
        $fs->remove($file);
    }

}