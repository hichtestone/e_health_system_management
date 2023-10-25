<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\Project;
use App\ESM\Repository\AuditTrail\ProjectAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProjectAuditTrailRepository::class)
 */
class ProjectAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\Project")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Project
     */
    private $entity;

    public function getEntity(): Project
    {
        return $this->entity;
    }

    public function setEntity(Project $entity): void
    {
        $this->entity = $entity;
    }
}
