<?php

namespace App\ESM\FormHandler;

use App\ESM\Form\DocumentTrackingCenterType;
use Doctrine\ORM\EntityManagerInterface;

class DocumentTrackingCenterHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return DocumentTrackingCenterType::class;
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
