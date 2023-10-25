<?php

namespace App\ESM\Entity;

use App\ESM\Repository\ReportConfigRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReportConfigRepository::class)
 */
class ReportConfig implements AuditrailableInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ReportModelVersion::class, inversedBy="versionConfigs")
     *
     * @var ReportModelVersion
     */
    private $modelVersion;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="reportConfigs")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Project
     */
    private $project;

	/**
	 * @return string
	 */
    public function getModelName(): string
    {
        return $this->getModelVersion()->getReportModel()->getName();
    }

	/**
	 * @return int|null
	 */
    public function getId(): ?int
    {
        return $this->id;
    }

	/**
	 * @return array
	 */
    public function getFieldsToBeIgnored(): array
    {
        return [];
    }

	/**
	 * @return Project|null
	 */
    public function getProject(): ?Project
    {
        return $this->project;
    }

	/**
	 * @param Project $project
	 */
    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

	/**
	 * @return ReportModelVersion
	 */
	public function getModelVersion(): ReportModelVersion
	{
		return $this->modelVersion;
	}

	/**
	 * @param ReportModelVersion $modelVersion
	 */
    public function setModelVersion(ReportModelVersion $modelVersion): void
    {
        $this->modelVersion = $modelVersion;
    }
}
