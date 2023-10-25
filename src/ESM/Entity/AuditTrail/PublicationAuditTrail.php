<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\Publication;
use App\ESM\Repository\AuditTrail\PublicationAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PublicationAuditTrailRepository::class)
 */
class PublicationAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\Publication")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Publication
     */
    private $entity;

    public function getEntity(): Publication
    {
        return $this->entity;
    }

    public function setEntity(Publication $entity): void
    {
        $this->entity = $entity;
    }
}
