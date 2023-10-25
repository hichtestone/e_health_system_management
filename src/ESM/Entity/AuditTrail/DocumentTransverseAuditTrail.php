<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\DocumentTransverse;
use App\ESM\Repository\AuditTrail\DocumentTransverseAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DocumentTransverseAuditTrailRepository::class)
 */
class DocumentTransverseAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\DocumentTransverse")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var DocumentTransverse
     */
    private $entity;

    public function getEntity(): DocumentTransverse
    {
        return $this->entity;
    }

    public function setEntity(DocumentTransverse $entity): void
    {
        $this->entity = $entity;
    }
}
