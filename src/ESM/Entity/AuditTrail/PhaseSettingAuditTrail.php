<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\PhaseSetting;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PhaseSettingAuditTrailRepository::class)
 */
class PhaseSettingAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\PhaseSetting")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var PhaseSetting
     */
    private $entity;

    public function getEntity(): PhaseSetting
    {
        return $this->entity;
    }

    public function setEntity(PhaseSetting $entity): void
    {
        $this->entity = $entity;
    }
}
