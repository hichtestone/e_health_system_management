<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\User;
use App\ESM\Repository\AuditTrail\UserAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserAuditTrailRepository::class)
 */
class UserAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var User
     */
    private $entity;

    public function getEntity(): User
    {
        return $this->entity;
    }

    public function setEntity(User $entity): void
    {
        $this->entity = $entity;
    }
}
