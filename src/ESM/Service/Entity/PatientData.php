<?php


namespace App\ESM\Service\Entity;

use Doctrine\ORM\EntityManagerInterface;

class PatientData
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function isOtherDataArchived(int $patient_id): bool
    {

        /** @var PatientData $patient */
        $patientData = $this->entityManager->getRepository(\App\ESM\Entity\PatientData::class)->findOneBy(['patient' => $patient_id]);

        if (null == $patientData) {
            return false;
        }

        if (null == $patientData->getDeletedAt()) {
            return false;
        }

        return true;

    }
}
