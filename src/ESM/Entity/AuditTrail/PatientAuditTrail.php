<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\Patient;
use App\ESM\Repository\AuditTrail\PatientAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PatientAuditTrailRepository::class)
 */
class PatientAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\Patient")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Patient
     */
    private $entity;

    public function getEntity(): Patient
    {
        return $this->entity;
    }

    public function setEntity(Patient $entity): void
    {
        $this->entity = $entity;
    }
}
