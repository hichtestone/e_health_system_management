<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\ProjectDatabaseFreeze;
use App\ESM\Repository\AuditTrail\DateFreezeAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DateFreezeAuditTrailRepository::class)
 */
class ProjectDatabaseFreezeAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\ProjectDatabaseFreeze")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var ProjectDatabaseFreeze
     */
    private $entity;

    public function getEntity(): ProjectDatabaseFreeze
    {
        return $this->entity;
    }

    public function setEntity(ProjectDatabaseFreeze $entity): void
    {
        $this->entity = $entity;
    }
}
