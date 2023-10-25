<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\Visit;
use App\ESM\Repository\AuditTrail\VisitAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VisitAuditTrailRepository::class)
 */
class VisitAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\Visit")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Visit
     */
    private $entity;

    public function getEntity(): Visit
    {
        return $this->entity;
    }

    public function setEntity(Visit $entity): void
    {
        $this->entity = $entity;
    }
}
