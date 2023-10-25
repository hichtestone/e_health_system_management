<?php

namespace App\ESM\Doctrine;

use App\ESM\Entity\Date;
use App\ESM\Entity\Project;
use App\ESM\Entity\Rule;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Class DateEntity
 * @package App\ESM\Doctrine
 */
class ProjectEvent
{
    /**
     * After Created.
     */
    public function postPersist(LifecycleEventArgs $args): bool
    {
        return $this->update($args);
    }

    private function update(LifecycleEventArgs $args): bool
    {

        /** @var Project $entity */
        $entity = $args->getObject();

        if (!$entity instanceof Project) {
            return false;
        }

        // Create new Date entity
        $date = new Date();
        $date->setProject($entity);

        // Create new Rule entity
        $rule = new Rule();
        $rule->setProject($entity);

        $entityManager = $args->getObjectManager();

        // Persist and flush
        $entityManager->persist($date);
        $entityManager->persist($rule);

        $entityManager->flush();

        return true;
    }
}
