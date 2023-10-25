<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\Point;
use App\ESM\Repository\AuditTrail\PointAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PointAuditTrailRepository::class)
 */
class PointAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\Point")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Point
     */
    private $entity;

    public function getEntity(): Point
    {
        return $this->entity;
    }

    public function setEntity(Point $entity): void
    {
        $this->entity = $entity;
    }
}
