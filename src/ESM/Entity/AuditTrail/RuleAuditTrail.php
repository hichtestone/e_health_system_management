<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\Rule;
use App\ESM\Repository\AuditTrail\RuleAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RuleAuditTrailRepository::class)
 */
class RuleAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\Rule")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Rule
     */
    private $entity;

    public function getEntity(): Rule
    {
        return $this->entity;
    }

    public function setEntity(Rule $entity): void
    {
        $this->entity = $entity;
    }
}
