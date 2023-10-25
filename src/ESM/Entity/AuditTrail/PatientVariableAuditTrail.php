<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\PatientVariable;
use App\ESM\Repository\AuditTrail\PatientVariableAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PatientVariableAuditTrailRepository::class)
 */
class PatientVariableAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\PatientVariable")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var PatientVariable
     */
    private $entity;

    public function getEntity(): PatientVariable
    {
        return $this->entity;
    }

    public function setEntity(PatientVariable $entity): void
    {
        $this->entity = $entity;
    }
}
