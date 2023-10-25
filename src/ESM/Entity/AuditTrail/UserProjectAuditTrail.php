<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\UserProject;
use App\ESM\Repository\AuditTrail\UserProjectAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserProjectAuditTrailRepository::class)
 */
class UserProjectAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\UserProject")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var UserProject
     */
    private $entity;

    public function getEntity(): UserProject
    {
        return $this->entity;
    }

    public function setEntity(UserProject $entity): void
    {
        $this->entity = $entity;
    }
}
