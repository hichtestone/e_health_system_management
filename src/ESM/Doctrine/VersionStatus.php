<?php

namespace App\ESM\Doctrine;

use App\ESM\Entity\VersionDocumentTransverse;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Class VersionStatus
 * @package App\Doctrine
 */
class VersionStatus
{
    /**
     * After Created.
     */
    public function postPersist(LifecycleEventArgs $args): bool
    {
        return $this->update($args);
    }

    /**
     * After Updated.
     */
    public function postUpdate(LifecycleEventArgs $args): bool
    {
        return $this->update($args);
    }

    private function update(LifecycleEventArgs $args): bool
    {
        $update = false;

        /** @var VersionDocumentTransverse $entity */
        $entity = $args->getObject();

        $class = get_class($entity);
        switch ($class) {
            case "App\ESM\Entity\VersionDocumentTransverse":
                $update = true;
                break;
        }

        if (!$update) {
            return false;
        }

        // La version est valide - le document tranverse doit etre aussi valide
        if (true === $entity->getIsValid()) {
            $entity->getDocumentTransverse()->setIsValid(true);
        }

        // La version n'est pas valide
        if (false === $entity->getIsValid()) {
            // si au moins une version appartenant a un document est valide - le document transverse est valide
            $versions_docs = $args->getObjectManager()->getRepository(VersionDocumentTransverse::class)->findBy([
                'documentTransverse' => $entity->getDocumentTransverse(),
            ]);

            // nombre de versions valide
            $valid_count = 0;
            foreach ($versions_docs as $versions_doc) {
                if (true === $versions_doc->getIsValid()) {
                    ++$valid_count;
                }
            }

            if (0 < $valid_count) {
                $entity->getDocumentTransverse()->setIsValid(true);
            } else {
                $entity->getDocumentTransverse()->setIsValid(false);
            }
        }

        // Persist and flush
        $args->getObjectManager()->persist($entity);
        $args->getObjectManager()->flush();

        return true;
    }
}
