<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\ReportConfig;
use App\ESM\Repository\AuditTrail\ReportConfigAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReportConfigAuditTrailRepository::class)
 */
class ReportConfigAuditTrail extends AbstractAuditTrailEntity
{
	/**
	 * @ORM\ManyToOne(targetEntity="App\ESM\Entity\ReportConfig")
	 * @ORM\JoinColumn(nullable=false)
	 *
	 * @var ReportConfig
	 */
	private $entity;

	public function getEntity(): ReportConfig
	{
		return $this->entity;
	}

	public function setEntity(ReportConfig $entity): void
	{
		$this->entity = $entity;
	}
}