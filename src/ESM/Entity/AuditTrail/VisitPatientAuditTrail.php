<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\VisitPatient;
use App\ESM\Repository\AuditTrail\VisitPatientAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VisitPatientAuditTrailRepository::class)
 */
class VisitPatientAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\VisitPatient")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var VisitPatient
     */
    private $entity;

    public function getEntity(): VisitPatient
    {
        return $this->entity;
    }

    public function setEntity(VisitPatient $entity): void
    {
        $this->entity = $entity;
    }
}
