<?php

namespace App\ETMF\Entity\AuditTrail;

use App\ETMF\Entity\Mailgroup;
use App\ETMF\Repository\AuditTrail\MailgroupAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MailgroupAuditTrailRepository::class)
 */
class MailgroupAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ETMF\Entity\Mailgroup")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Mailgroup
     */
    private $entity;

    public function getEntity(): Mailgroup
    {
        return $this->entity;
    }

    public function setEntity(Mailgroup $entity): void
    {
        $this->entity = $entity;
    }
}
