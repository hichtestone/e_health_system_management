<?php

namespace App\ETMF\Entity\AuditTrail;

use App\ETMF\Entity\DocumentVersion;
use App\ETMF\Repository\AuditTrail\DocumentVersionAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DocumentVersionAuditTrailRepository::class)
 */
class DocumentVersionAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ETMF\Entity\DocumentVersion")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var DocumentVersion
     */
    private $entity;

    public function getEntity(): DocumentVersion
    {
        return $this->entity;
    }

    public function setEntity(DocumentVersion $entity): void
    {
        $this->entity = $entity;
    }
}
