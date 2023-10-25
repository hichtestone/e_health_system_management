<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\Submission;
use App\ESM\Form\SubmissionType;
use Doctrine\ORM\EntityManagerInterface;

class SubmissionHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return SubmissionType::class;
    }

    /**
     * @param Submission $data
     */
    protected function process($data): void
    {
        $uow = $this->entityManager->getUnitOfWork();
        $originalData = $uow->getOriginalEntityData($data);

        if (empty($originalData) ||
            $data->getAdmissibilityAt() != $originalData['admissibilityAt']
        || $data->getQuestion() != $originalData['question']) {
            $data->computeAuthorizationDeadlineAt();
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
