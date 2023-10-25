<?php


namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\DeviationSampleCorrection;
use App\ESM\Repository\AuditTrail\DeviationSampleCorrectionAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeviationSampleCorrectionAuditTrailRepository::class)
 */
class DeviationSampleCorrectionAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\DeviationSampleCorrection")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var DeviationSampleCorrection
     */
    private $entity;

	/**
	 * @return DeviationSampleCorrection
	 */
    public function getEntity(): DeviationSampleCorrection
    {
        return $this->entity;
    }

	/**
	 * @param DeviationSampleCorrection $entity
	 */
    public function setEntity(DeviationSampleCorrection $entity): void
    {
        $this->entity = $entity;
    }
}
