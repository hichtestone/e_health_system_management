<?php

namespace App\ESM\FormHandler;

use App\ESM\Form\DatabaseFreezeType;
use Doctrine\ORM\EntityManagerInterface;

class DatabaseFreezeHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return DatabaseFreezeType::class;
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
