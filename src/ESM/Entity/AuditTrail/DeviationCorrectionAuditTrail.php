<?php


namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\DeviationCorrection;
use App\ESM\Repository\AuditTrail\DeviationCorrectionAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeviationCorrectionAuditTrailRepository::class)
 */
class DeviationCorrectionAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\DeviationCorrection")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var DeviationCorrection
     */
    private $entity;

    public function getEntity(): DeviationCorrection
    {
        return $this->entity;
    }

    public function setEntity(DeviationCorrection $entity): void
    {
        $this->entity = $entity;
    }
}
