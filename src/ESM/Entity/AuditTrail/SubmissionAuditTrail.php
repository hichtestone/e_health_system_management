<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\Submission;
use App\ESM\Repository\AuditTrail\SubmissionAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubmissionAuditTrailRepository::class)
 */
class SubmissionAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\Submission")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Submission
     */
    private $entity;

    public function getEntity(): Submission
    {
        return $this->entity;
    }

    public function setEntity(Submission $entity): void
    {
        $this->entity = $entity;
    }
}
