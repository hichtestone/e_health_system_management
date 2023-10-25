<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\DeviationSystemReview;
use App\ESM\Repository\AuditTrail\DeviationSystemReviewAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeviationSystemReviewAuditTrailRepository::class)
 */
class DeviationSystemReviewAuditTrail extends AbstractAuditTrailEntity
{
	/**
	 * @ORM\ManyToOne(targetEntity="App\ESM\Entity\DeviationSystemReview")
	 * @ORM\JoinColumn(nullable=false)
	 *
	 * @var DeviationSystemReview
	 */
	private $entity;

	/**
	 * @return DeviationSystemReview
	 */
	public function getEntity(): DeviationSystemReview
	{
		return $this->entity;
	}

	/**
	 * @param DeviationSystemReview $entity
	 */
	public function setEntity(DeviationSystemReview $entity): void
	{
		$this->entity = $entity;
	}
}
