<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\DeviationAction;
use App\ESM\Repository\AuditTrail\DeviationActionAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeviationActionAuditTrailRepository::class)
 */
class DeviationActionAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\DeviationAction")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var DeviationAction
     */
    private $entity;

    public function getEntity(): DeviationAction
    {
        return $this->entity;
    }

    public function setEntity(DeviationAction $entity): void
    {
        $this->entity = $entity;
    }
}
