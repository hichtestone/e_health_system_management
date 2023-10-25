<?php

namespace App\ETMF\Entity\AuditTrail;

use App\ETMF\Entity\Zone;
use App\ETMF\Repository\AuditTrail\ZoneAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ZoneAuditTrailRepository::class)
 */
class ZoneAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ETMF\Entity\Zone")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Zone
     */
    private $entity;

    public function getEntity(): Zone
    {
        return $this->entity;
    }

    public function setEntity(Zone $entity): void
    {
        $this->entity = $entity;
    }
}
