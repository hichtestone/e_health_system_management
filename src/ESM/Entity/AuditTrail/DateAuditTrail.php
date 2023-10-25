<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\Date;
use App\ESM\Repository\AuditTrail\DateAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DateAuditTrailRepository::class)
 */
class DateAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\Date")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Date
     */
    private $entity;

    public function getEntity(): Date
    {
        return $this->entity;
    }

    public function setEntity(Date $entity): void
    {
        $this->entity = $entity;
    }
}
