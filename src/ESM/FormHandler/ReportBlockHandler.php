<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\ReportBlock;
use App\ESM\Form\ReportBlockType;
use Doctrine\ORM\EntityManagerInterface;

class ReportBlockHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return ReportBlockType::class;
    }

    /**
     * @param ReportBlock $data
     */
    protected function process($data): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
