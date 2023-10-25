<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\Funding;
use App\ESM\Repository\AuditTrail\FundingAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FundingAuditTrailRepository::class)
 */
class FundingAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\Funding")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Funding
     */
    private $entity;

    public function getEntity(): Funding
    {
        return $this->entity;
    }

    public function setEntity(Funding $entity): void
    {
        $this->entity = $entity;
    }
}
