<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\User;
use App\ESM\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;

class UserHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return UserType::class;
    }

    /**
     * @param User $data
     */
    protected function process($data): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
