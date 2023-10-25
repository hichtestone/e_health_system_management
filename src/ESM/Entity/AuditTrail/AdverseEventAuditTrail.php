<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\AdverseEvent;
use App\ESM\Repository\AuditTrail\AdverseEventAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AdverseEventAuditTrailRepository::class)
 */
class AdverseEventAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\AdverseEvent")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var AdverseEvent
     */
    private $entity;

    public function getEntity(): AdverseEvent
    {
        return $this->entity;
    }

    public function setEntity(AdverseEvent $entity): void
    {
        $this->entity = $entity;
    }
}
