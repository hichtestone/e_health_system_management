<?php

namespace App\ETMF\Entity\AuditTrail;

use App\ETMF\Entity\Tag;
use App\ETMF\Repository\AuditTrail\TagAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TagAuditTrailRepository::class)
 */
class TagAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ETMF\Entity\Tag")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Tag
     */
    private $entity;

    public function getEntity(): Tag
    {
        return $this->entity;
    }

    public function setEntity(Tag $entity): void
    {
        $this->entity = $entity;
    }
}
