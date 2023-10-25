<?php

namespace App\ESM\Doctrine;

use App\ESM\Entity\DocumentTransverse;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Class DocumentStatus
 * @package App\Doctrine
 */
class DocumentStatus
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

        /** @var DocumentTransverse $entity */
        $entity = $args->getObject();

        $class = get_class($entity);
        switch ($class) {
            case "App\ESM\Entity\DocumentTransverse":
                $update = true;
                break;
        }

        if (!$update) {
            return false;
        }

        // Le document est valide - le medicament doit etre aussi valide
        if (true === $entity->getIsValid() && null != $entity->getDrug()) {
            $entity->getDrug()->setIsValid(true);
        }

        // Le document n'est pas valide
        if (false === $entity->getIsValid() && null != $entity->getDrug()) {

            // si au moins un document appartenant a un medicament est valide - le medicament transverse est valide
            $drug_docs = $args->getObjectManager()->getRepository(DocumentTransverse::class)->findBy([
                'drug' => $entity->getDrug(),
            ]);

            // nombre de versions valide
            $valid_count = 0;
            foreach ($drug_docs as $drug) {
                if (true === $drug->getIsValid()) {
                    ++$valid_count;
                }
            }

            if (0 < $valid_count) {
                $entity->getDrug()->setIsValid(true);
            } else {
                $entity->getDrug()->setIsValid(false);
            }
        }

        // Persist and flush
        $args->getObjectManager()->persist($entity);
        $args->getObjectManager()->flush();
        return true;
    }


    }
