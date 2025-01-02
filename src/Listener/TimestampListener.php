<?php

namespace Spyck\AutomationBundle\Listener;

use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Spyck\AutomationBundle\Entity\TimestampInterface;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
final class TimestampListener
{
    public function prePersist(PrePersistEventArgs $prePersistEventArgs): void
    {
        $object = $prePersistEventArgs->getObject();

        if ($object instanceof TimestampInterface) {
            $object->setTimestampCreated(new DateTimeImmutable());
        }
    }

    public function preUpdate(PreUpdateEventArgs $preUpdateEventArgs): void
    {
        $object = $preUpdateEventArgs->getObject();

        if ($object instanceof TimestampInterface) {
            $object->setTimestampUpdated(new DateTimeImmutable());
        }
    }
}
