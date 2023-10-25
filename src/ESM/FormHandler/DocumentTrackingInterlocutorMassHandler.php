<?php

namespace App\ESM\FormHandler;

use App\ESM\Form\DocumentTrackingInterlocutorMassType;
use Doctrine\ORM\EntityManagerInterface;

class DocumentTrackingInterlocutorMassHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return DocumentTrackingInterlocutorMassType::class;
    }

    /**
     * @param $data
     */
    protected function process($data): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
