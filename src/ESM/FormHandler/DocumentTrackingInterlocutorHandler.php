<?php

namespace App\ESM\FormHandler;

use App\ESM\Form\DocumentTrackingInterlocutorType;
use Doctrine\ORM\EntityManagerInterface;

class DocumentTrackingInterlocutorHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return DocumentTrackingInterlocutorType::class;
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
