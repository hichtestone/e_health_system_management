<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\DocumentTrackingInterlocutor;
use App\ESM\Repository\AuditTrail\DocumentTrackingInterlocutorAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DocumentTrackingInterlocutorAuditTrailRepository::class)
 */
class DocumentTrackingInterlocutorAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\DocumentTrackingInterlocutor")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var DocumentTrackingInterlocutor
     */
    private $entity;

    public function getEntity(): DocumentTrackingInterlocutor
    {
        return $this->entity;
    }

    public function setEntity(DocumentTrackingInterlocutor $entity): void
    {
        $this->entity = $entity;
    }
}
