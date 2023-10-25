<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\DeviationSystemAction;
use App\ESM\Repository\AuditTrail\DeviationSystemActionAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeviationSystemActionAuditTrailRepository::class)
 */
class DeviationSystemActionAuditTrail extends AbstractAuditTrailEntity
{
	/**
	 * @ORM\ManyToOne(targetEntity="App\ESM\Entity\DeviationSystemAction")
	 * @ORM\JoinColumn(nullable=false)
	 *
	 * @var DeviationSystemAction
	 */
	private $entity;

	/**
	 * @return DeviationSystemAction
	 */
	public function getEntity(): DeviationSystemAction
	{
		return $this->entity;
	}

	/**
	 * @param DeviationSystemAction $entity
	 */
	public function setEntity(DeviationSystemAction $entity): void
	{
		$this->entity = $entity;
	}
}
