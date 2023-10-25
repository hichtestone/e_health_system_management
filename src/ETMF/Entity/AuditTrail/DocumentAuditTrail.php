<?php

namespace App\ETMF\Entity\AuditTrail;

use App\ETMF\Entity\Document;
use App\ETMF\Repository\AuditTrail\DocumentAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DocumentAuditTrailRepository::class)
 */
class DocumentAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ETMF\Entity\Document")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Document
     */
    private $entity;

    public function getEntity(): Document
    {
        return $this->entity;
    }

    public function setEntity(Document $entity): void
    {
        $this->entity = $entity;
    }
}
