<?php

namespace App\ESM\Doctrine;

use Doctrine\ORM\Event\PreFlushEventArgs;

/**
 * Class DeletedAtEvent.
 */
class CreatedAtEvent
{
    public function preFlush(PreFlushEventArgs $event): void
    {
        // autofill createdAt si prop exists
        $em = $event->getEntityManager();
        foreach ($em->getUnitOfWork()->getScheduledEntityInsertions() as $object) {
            if (method_exists($object, 'setCreatedAt') && method_exists($object, 'getCreatedAt')) {
                if (null === $object->getCreatedAt()) {
                    $object->setCreatedAt(new \DateTime());
                }
            }
        }
    }
}
