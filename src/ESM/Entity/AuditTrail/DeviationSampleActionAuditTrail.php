<?php


namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\DeviationSampleAction;
use App\ESM\Repository\AuditTrail\DeviationSampleActionAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeviationSampleActionAuditTrailRepository::class)
 */
class DeviationSampleActionAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\DeviationSampleAction")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var DeviationSampleAction
     */
    private $entity;

	/**
	 * @return DeviationSampleAction
	 */
    public function getEntity(): DeviationSampleAction
    {
        return $this->entity;
    }

	/**
	 * @param DeviationSampleAction $entity
	 */
    public function setEntity(DeviationSampleAction $entity): void
    {
        $this->entity = $entity;
    }
}
