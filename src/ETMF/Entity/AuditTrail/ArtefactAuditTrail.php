<?php

namespace App\ETMF\Entity\AuditTrail;

use App\ETMF\Repository\AuditTrail\ArtefactAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use App\ETMF\Entity\Artefact;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArtefactAuditTrailRepository::class)
 */
class ArtefactAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ETMF\Entity\Artefact")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Artefact
     */
    private $entity;

    public function getEntity(): Artefact
    {
        return $this->entity;
    }

    public function setEntity(Artefact $entity): void
    {
        $this->entity = $entity;
    }
}
