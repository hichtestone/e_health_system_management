<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\Interlocutor;
use App\ESM\Repository\AuditTrail\InterlocutorAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InterlocutorAuditTrailRepository::class)
 */
class InterlocutorAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\Interlocutor")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Interlocutor
     */
    private $entity;

    public function getEntity(): Interlocutor
    {
        return $this->entity;
    }

    public function setEntity(Interlocutor $entity): void
    {
        $this->entity = $entity;
    }
}
