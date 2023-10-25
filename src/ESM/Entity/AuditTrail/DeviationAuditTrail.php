<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\Deviation;
use App\ESM\Repository\AuditTrail\DeviationAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeviationAuditTrailRepository::class)
 */
class DeviationAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\Deviation")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Deviation
     */
    private $entity;

    public function getEntity(): Deviation
    {
        return $this->entity;
    }

    public function setEntity(Deviation $entity): void
    {
        $this->entity = $entity;
    }
}
