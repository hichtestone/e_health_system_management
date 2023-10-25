<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\VersionDocumentTransverse;
use App\ESM\Repository\AuditTrail\VersionDocumentTransverseAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VersionDocumentTransverseAuditTrailRepository::class)
 */
class VersionDocumentTransverseAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\VersionDocumentTransverse")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var VersionDocumentTransverse
     */
    private $entity;

    public function getEntity(): VersionDocumentTransverse
    {
        return $this->entity;
    }

    public function setEntity(VersionDocumentTransverse $entity): void
    {
        $this->entity = $entity;
    }
}
