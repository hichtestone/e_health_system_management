<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\ReportModel;
use App\ESM\Repository\AuditTrail\ReportModelAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReportModelAuditTrailRepository::class)
 */
class ReportModelAuditTrail extends AbstractAuditTrailEntity
{
	/**
	 * @ORM\ManyToOne(targetEntity="App\ESM\Entity\ReportModel")
	 * @ORM\JoinColumn(nullable=false)
	 *
	 * @var ReportModel
	 */
	private $entity;

	public function getEntity(): ReportModel
	{
		return $this->entity;
	}

	public function setEntity(ReportModel $entity): void
	{
		$this->entity = $entity;
	}
}