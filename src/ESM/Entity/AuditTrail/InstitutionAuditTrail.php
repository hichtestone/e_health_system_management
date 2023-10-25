<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\Institution;
use App\ESM\Repository\AuditTrail\InstitutionAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InstitutionAuditTrailRepository::class)
 */
class InstitutionAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\Institution")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Institution
     */
    private $entity;

    public function getEntity(): Institution
    {
        return $this->entity;
    }

    public function setEntity(Institution $entity): void
    {
        $this->entity = $entity;
    }
}
