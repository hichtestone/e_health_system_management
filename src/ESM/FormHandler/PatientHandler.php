<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\Patient;
use App\ESM\Form\PatientType;
use Doctrine\ORM\EntityManagerInterface;

class PatientHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return PatientType::class;
    }

    /**
     * @param Patient $data
     */
    protected function process($data): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
