<?php


namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\Drug;
use App\ESM\Repository\AuditTrail\DrugAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DrugAuditTrailRepository::class)
 */
class DrugAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\Drug")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Drug
     */
    private $entity;

    public function getEntity(): Drug
    {
        return $this->entity;
    }

    public function setEntity(Drug $entity): void
    {
        $this->entity = $entity;
    }
}
