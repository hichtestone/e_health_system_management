<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\DeviationCorrection;
use App\ESM\Form\DeviationCorrectionType;
use Doctrine\ORM\EntityManagerInterface;

class DeviationCorrectionHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return DeviationCorrectionType::class;
    }

    /**
     * @param DeviationCorrection $data
     */
    protected function process($data): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
