<?php


namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\DeviationSample;
use App\ESM\Repository\AuditTrail\DeviationSampleAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeviationSampleAuditTrailRepository::class)
 */
class DeviationSampleAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\DeviationSample")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var DeviationSample
     */
    private $entity;

	/**
	 * @return DeviationSample
	 */
    public function getEntity(): DeviationSample
    {
        return $this->entity;
    }

	/**
	 * @param DeviationSample $entity
	 */
    public function setEntity(DeviationSample $entity): void
    {
        $this->entity = $entity;
    }
}
