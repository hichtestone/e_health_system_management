<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\CourbeSetting;
use App\ESM\Repository\AuditTrail\CourbeSettingAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CourbeSettingAuditTrailRepository::class)
 */
class CourbeSettingAuditTrail extends AbstractAuditTrailEntity
{
    /**
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\CourbeSetting")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var CourbeSetting
     */
    private $entity;

    public function getEntity(): CourbeSetting
    {
        return $this->entity;
    }

    public function setEntity(CourbeSetting $entity): void
    {
        $this->entity = $entity;
    }
}
