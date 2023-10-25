<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\ReportVisit;
use App\ESM\Form\ReportVisitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ReportVisitHandler extends AbstractFormHandler
{
    private $entityManager;
    private $user;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->user = $tokenStorage->getToken()->getUser();
    }

    protected function getFormType(): string
    {
        return ReportVisitType::class;
    }

    /**
     * @param ReportVisit $data
     */
    protected function process($data): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
