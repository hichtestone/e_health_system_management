<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\InterlocutorCenter;
use App\ESM\Repository\AuditTrail\InterlocutorCenterAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InterlocutorCenterAuditTrailRepository::class)
 */
class InterlocutorCenterAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\InterlocutorCenter")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var InterlocutorCenter
     */
    private $entity;

    public function getEntity(): InterlocutorCenter
    {
        return $this->entity;
    }

    public function setEntity(InterlocutorCenter $entity): void
    {
        $this->entity = $entity;
    }
}
