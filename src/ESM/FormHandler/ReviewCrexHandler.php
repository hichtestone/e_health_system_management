<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\DeviationReview;
use App\ESM\Form\RevueCrexType;
use Doctrine\ORM\EntityManagerInterface;

class ReviewCrexHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return RevueCrexType::class;
    }

    /**
     * @param DeviationReview $data
     */
    protected function process($data): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
