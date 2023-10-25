<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\DeviationSampleCorrection;
use App\ESM\Form\DeviationSampleCorrectionType;
use Doctrine\ORM\EntityManagerInterface;

class DeviationSampleCorrectionHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return DeviationSampleCorrectionType::class;
    }

    /**
     * @param DeviationSampleCorrection $data
     */
    protected function process($data): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
