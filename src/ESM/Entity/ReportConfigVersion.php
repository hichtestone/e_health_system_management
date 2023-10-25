<?php

namespace App\ESM\Entity;

use App\ESM\Repository\ReportConfigVersionRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=ReportConfigVersionRepository::class)
 */
class ReportConfigVersion implements AuditrailableInterface
{
	public const STATUS_INACTIVE = 0;
	public const STATUS_ACTIVE	 = 1;
	public const STATUS_OUTDATED = 2;
	public const STATUS = [
		self::STATUS_INACTIVE => 'inactive',
		self::STATUS_ACTIVE   => 'active',
		self::STATUS_OUTDATED => 'outdated',
	];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=false)
     *
     * @var int
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=ReportConfig::class)
     *
     * @var ReportConfig
     */
    private $config;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $numberVersion;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reportConfigVersions")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
	 *
	 * @var
	 */
	private $configuredBy;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 *
	 * @var DateTime|null
	 */
	private $startedAt;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 *
	 * @var DateTime|null
	 */
	private $endedAt;

    /**
     * @ORM\OneToMany(targetEntity=ReportVisit::class, mappedBy="reportConfigVersion")
     *
     * @var ArrayCollection<int, ReportVisit>
     */
    private $reportVisits;

    /**
     * @ORM\OneToMany(targetEntity=ReportConfigVersionBlock::class, mappedBy="configVersion")
     *
     * @var ArrayCollection<int, ReportConfigVersionBlock>
     */
    private $versionBlocks;

    /**
     * ReportConfig constructor.
     */
    public function __construct()
    {
        $this->reportVisits = new ArrayCollection();
        $this->versionBlocks = new ArrayCollection();
    }

	/**
	 * @return int|null
	 */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): ?string
	{
		return $this->getModelNameAndPublishedVersion();
	}

	/**
	 * @return array
	 */
    public function getFieldsToBeIgnored(): array
    {
        return [];
    }

	/**
	 * @return int
	 */
	public function getStatus(): int
	{
		return $this->status;
	}

	/**
	 * @throws Exception
	 */
	public function setStatus(int $status): void
    {
    	if (!array_key_exists($status, self::STATUS)) {
    		throw new Exception('This status : ' . $status . ' does not exist!');
		}

        $this->status = $status;
    }

	/**
	 * @return ReportModel
	 */
    public function getConfigModel(): ReportModel
    {
        return $this->config->getModelVersion()->getReportModel();
    }

	/**
	 * @return ReportConfig
	 */
    public function getConfig(): ReportConfig
    {
        return $this->config;
    }

	/**
	 * @param ReportConfig $config
	 */
    public function setConfig(ReportConfig $config): void
    {
        $this->config = $config;
    }

	/**
	 * @return int
	 */
	public function getNumberVersion(): int
	{
		return $this->numberVersion;
	}

	/**
	 * @return $this
	 */
	public function setNumberVersion(int $numberVersion): self
	{
		$this->numberVersion = $numberVersion;

        return $this;
    }

	/**
	 * @return User|null
	 */
	public function getConfiguredBy(): ?User
	{
		return $this->configuredBy;
	}

	/**
	 * @param User|null $configuredBy
	 */
	public function setConfiguredBy(?User $configuredBy): void
	{
		$this->configuredBy = $configuredBy;
	}

	/**
	 * @return DateTime|null
	 */
	public function getStartedAt(): ?DateTime
	{
		return $this->startedAt;
	}

	/**
	 * @param DateTime|null $startedAt
	 * @return $this
	 */
	public function setStartedAt(?DateTime $startedAt): self
	{
		$this->startedAt = $startedAt;

		return $this;
	}

	/**
	 * @return DateTime|null
	 */
	public function getEndedAt(): ?DateTime
	{
		return $this->endedAt;
	}

	/**
	 * @param DateTime|null $endedAt
	 * @return $this
	 */
	public function setEndedAt(?DateTime $endedAt): self
	{
		$this->endedAt = $endedAt;

		return $this;
	}

    /**
     * @return Collection|ReportVisit[]
     */
    public function getReportVisits(): Collection
    {
        return $this->reportVisits;
    }

	/**
	 * @param ReportVisit $reportVisit
	 * @return $this
	 */
    public function addReportVisit(ReportVisit $reportVisit): self
    {
        if (!$this->reportVisits->contains($reportVisit)) {
            $this->reportVisits[] = $reportVisit;
            $reportVisit->setProject($this);
        }

		return $this;
	}

	/**
	 * @param ReportVisit $reportVisit
	 * @return $this
	 */
    public function removeReportVisit(ReportVisit $reportVisit): self
    {
        if ($this->reportVisits->removeElement($reportVisit)) {
            // set the owning side to null (unless already changed)
            if ($reportVisit->getProject() === $this) {
                $reportVisit->setProject(null);
            }
        }
    }


	/**
	 * @return Collection
	 */
    public function getVersionBlocks(): Collection
    {
        return $this->versionBlocks;
    }

	/**
	 * @param ReportConfigVersionBlock $versionBlock
	 * @return $this
	 */
	public function addVersionBlock(ReportConfigVersionBlock $versionBlock): self
	{
		if (!$this->versionBlocks->contains($versionBlock)) {
			$this->versionBlocks[] = $versionBlock;
			$versionBlock->setConfigVersion($this);
		}

		return $this;
	}

	/**
	 * @param ReportConfigVersionBlock $versionBlock
	 * @return $this
	 */
	public function removeVersionBlock(ReportConfigVersionBlock $versionBlock): self
	{
		if ($this->versionBlocks->removeElement($versionBlock)) {
			// set the owning side to null (unless already changed)
			if ($versionBlock->getConfigVersion() === $this) {
				$versionBlock->setConfigVersion(null);
			}
		}

		return $this;
	}

	/**
	 * @return string
	 */
	public function getModelName(): string
    {
        return $this->getConfig()->getModelVersion()->getReportModel()->getName();
    }

	/**
	 * @return int
	 */
	public function getModelVersion(): int
	{
		return $this->getConfig()->getModelVersion()->getNumberVersion();
	}

	public function getModelNameAndPublishedVersion(): string
	{
		return $this->getModelName() . ' v' . $this->getModelPublishedVersion() . '.' . $this->getNumberVersion();
	}

	/**
	 * @return string
	 */
	public function getModelType(): string
	{
		return $this->getConfig()->getModelVersion()->getReportModel()->getReportType();
	}

	/**
	 * @return string
	 */
	public function getModelTypeVisit(): string
	{
		return $this->getConfig()->getModelVersion()->getReportModel()->getVisitType();
	}

	/**
	 * @return string
	 */
    public function getModelPublishedVersion(): string
    {
        return $this->getConfig()->getModelVersion()->getNumberVersion();
    }

	/**
	 * @return int
	 */
	public function getModelStatus(): int
	{
		return $this->getConfig()->getModelVersion()->getStatus();
	}

	/**
	 * @return DateTime
	 */
	public function getModelPublishedAt(): ?DateTime
	{
		return $this->getConfig()->getModelVersion()->getPublishedAt();
	}

	/**
	 * @return DateTime
	 */
	public function getModelDeletedAt(): ?DateTime
	{
		return $this->getConfig()->getModelVersion()->getReportModel()->getDeletedAt() ?? null;
	}
}
