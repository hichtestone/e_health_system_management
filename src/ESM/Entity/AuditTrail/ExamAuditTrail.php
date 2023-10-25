<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\Exam;
use App\ESM\Repository\AuditTrail\ExamAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ExamAuditTrailRepository::class)
 */
class ExamAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\Exam")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Exam
     */
    private $entity;

    public function getEntity(): Exam
    {
        return $this->entity;
    }

    public function setEntity(Exam $entity): void
    {
        $this->entity = $entity;
    }
}
