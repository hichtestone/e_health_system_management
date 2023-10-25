<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\DocumentTracking;
use App\ESM\Repository\AuditTrail\DocumentTrackingAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DocumentTrackingAuditTrailRepository::class)
 */
class DocumentTrackingAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\DocumentTracking")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var DocumentTracking
     */
    private $entity;

    public function getEntity(): DocumentTracking
    {
        return $this->entity;
    }

    public function setEntity(DocumentTracking $entity): void
    {
        $this->entity = $entity;
    }
}
