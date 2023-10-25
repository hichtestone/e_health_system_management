<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\PatientData;
use App\ESM\Repository\AuditTrail\PatientDataAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PatientDataAuditTrailRepository::class)
 */
class PatientDataAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\PatientData")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var PatientData
     */
    private $entity;

    public function getEntity(): PatientData
    {
        return $this->entity;
    }

    public function setEntity(PatientData $entity): void
    {
        $this->entity = $entity;
    }
}
