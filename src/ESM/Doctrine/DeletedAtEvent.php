<?php

namespace App\ESM\Doctrine;

use Doctrine\ORM\Event\PreFlushEventArgs;

/**
 * Class DeletedAtEvent.
 */
class DeletedAtEvent
{
    public function preFlush(PreFlushEventArgs $event): void
    {
        // override la suppression par modif deleteAt si prop exists
        $em = $event->getEntityManager();
        foreach ($em->getUnitOfWork()->getScheduledEntityDeletions() as $object) {
            if (method_exists($object, 'getDeletedAt') && method_exists($object, 'setDeletedAt')) {
                if ($object->getDeletedAt() instanceof \Datetime) {
                    continue;
                } else {
                    $object->setDeletedAt(new \DateTime());
                    $em->merge($object);
                    $em->persist($object);
                }
            }
        }
    }
}
