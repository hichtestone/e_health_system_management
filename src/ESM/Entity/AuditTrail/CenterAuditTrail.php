<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\Center;
use App\ESM\Repository\AuditTrail\CenterAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CenterAuditTrailRepository::class)
 */
class CenterAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\Center")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Center
     */
    private $entity;

    public function getEntity(): Center
    {
        return $this->entity;
    }

    public function setEntity(Center $entity): void
    {
        $this->entity = $entity;
    }
}
