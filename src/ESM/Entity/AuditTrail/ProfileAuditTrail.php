<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\Profile;
use App\ESM\Repository\AuditTrail\ProfileAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProfileAuditTrailRepository::class)
 */
class ProfileAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\Profile")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Profile
     */
    private $entity;

    public function getEntity(): Profile
    {
        return $this->entity;
    }

    public function setEntity(Profile $entity): void
    {
        $this->entity = $entity;
    }
}
