<?php

namespace App\ETMF\Entity\AuditTrail;

use App\ETMF\Entity\Section;
use App\ETMF\Repository\AuditTrail\SectionAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SectionAuditTrailRepository::class)
 */
class SectionAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ETMF\Entity\Section")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Section
     */
    private $entity;

    public function getEntity(): Section
    {
        return $this->entity;
    }

    public function setEntity(Section $entity): void
    {
        $this->entity = $entity;
    }
}
