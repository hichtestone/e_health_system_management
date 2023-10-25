<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\ReportVisit;
use App\ESM\Repository\AuditTrail\ReportVisitAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReportVisitAuditTrailRepository::class)
 */
class ReportVisitAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\ReportVisit")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var ReportVisit
     */
    private $entity;

    public function getEntity(): ReportVisit
    {
        return $this->entity;
    }

    public function setEntity(ReportVisit $entity): void
    {
        $this->entity = $entity;
    }
}
