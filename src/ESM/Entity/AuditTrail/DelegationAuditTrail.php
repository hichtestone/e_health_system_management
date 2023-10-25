<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\Contact;
use App\ESM\Entity\Delegation;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DelegationAuditTrailRepository::class)
 */
class DelegationAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\Delegation")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Delegation
     */
    private $entity;

    public function getEntity(): Contact
    {
        return $this->entity;
    }

    public function setEntity(Delegation $entity): void
    {
        $this->entity = $entity;
    }
}
