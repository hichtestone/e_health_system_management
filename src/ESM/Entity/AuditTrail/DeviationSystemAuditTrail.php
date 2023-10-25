<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\DeviationSystem;
use App\ESM\Repository\AuditTrail\DeviationSystemAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeviationSystemAuditTrailRepository::class)
 */
class DeviationSystemAuditTrail extends AbstractAuditTrailEntity
{
	/**
	 * @ORM\ManyToOne(targetEntity="App\ESM\Entity\DeviationSystem")
	 * @ORM\JoinColumn(nullable=false)
	 *
	 * @var DeviationSystem
	 */
	private $entity;

	public function getEntity(): DeviationSystem
	{
		return $this->entity;
	}

	public function setEntity(DeviationSystem $entity): void
	{
		$this->entity = $entity;
	}
}
