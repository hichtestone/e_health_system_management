<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\ReportBlockParam;
use App\ESM\Form\ReportBlockParamType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ReportBlockParamHandler extends AbstractFormHandler
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
        return ReportBlockParamType::class;
    }


    /**
     * @param ReportBlockParam $data
     */
    protected function process($data): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
