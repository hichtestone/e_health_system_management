<?php

namespace App\ESM\FormHandler;

use App\ESM\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;

class ProfileHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return ProfileType::class;
    }

    /**
     * @param $data
     */
    protected function process($data): void
    {
        if (UnitOfWork::STATE_NEW === $this->entityManager->getUnitOfWork()->getEntityState($data)) {
            $this->entityManager->persist($data);
        }
        $this->entityManager->flush();
    }
}
