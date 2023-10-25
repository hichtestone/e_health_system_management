<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\DocumentTrackingCenter;
use App\ESM\Repository\AuditTrail\DocumentTrackingCenterAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DocumentTrackingCenterAuditTrailRepository::class)
 */
class DocumentTrackingCenterAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\DocumentTrackingCenter")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var DocumentTrackingCenter
     */
    private $entity;

    public function getEntity(): DocumentTrackingCenter
    {
        return $this->entity;
    }

    public function setEntity(DocumentTrackingCenter $entity): void
    {
        $this->entity = $entity;
    }
}
