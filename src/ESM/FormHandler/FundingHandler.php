<?php

namespace App\ESM\FormHandler;

use App\ESM\Form\FundingType;
use Doctrine\ORM\EntityManagerInterface;

class FundingHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return FundingType::class;
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
