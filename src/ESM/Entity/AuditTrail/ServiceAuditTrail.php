<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\Service;
use App\ESM\Repository\AuditTrail\ServiceAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ServiceAuditTrailRepository::class)
 */
class ServiceAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\Service")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Service
     */
    private $entity;

    public function getEntity(): Service
    {
        return $this->entity;
    }

    public function setEntity(Service $entity): void
    {
        $this->entity = $entity;
    }
}
