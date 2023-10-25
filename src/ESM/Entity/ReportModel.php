<?php

namespace App\ESM\Entity;

use App\ESM\Repository\ReportModelRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReportModelRepository::class)
 */
class ReportModel implements AuditrailableInterface
{
    public const REPORT_IN_FOLLOW_UP = 0;
    public const REPORT_PROD = 1;
    public const REPORT_CLOS = 2;
    public const REPORT_TYPE = [
        self::REPORT_IN_FOLLOW_UP => 'entity.report_model.field.report_type.in_follow_up',
        self::REPORT_PROD => 'entity.report_model.field.report_type.prod',
        self::REPORT_CLOS => 'entity.report_model.field.report_type.clos',
    ];

    public const VISIT_ON_SITE = 0;
    public const VISIT_OFF_SITE = 1;
    public const VISIT_TYPE = [
        self::VISIT_ON_SITE => 'entity.report_model.field.visit_type.on_site',
        self::VISIT_OFF_SITE => 'entity.report_model.field.visit_type.off_site',
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $reportType;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $visitType;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @ORM\OneToMany(targetEntity=ReportModelVersion::class, mappedBy="reportModel")
     *
     * @var ArrayCollection<int, ReportModelVersion>
     */
    private $versions;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $deletedAt;

    public function __construct()
    {
        $this->versions = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getReportType(): ?int
    {
        return $this->reportType;
    }

	/**
	 * @param int $reportType
	 *
	 * @return void
	 * @throws \Exception
	 */
    public function setReportType(int $reportType): void
    {
        if (!array_key_exists($reportType, self::REPORT_TYPE) && null !== $reportType) {
            throw new \Exception('type model  '.$reportType.' not authorised !');
        }
        $this->reportType = $reportType;
    }

    public function getVisitType(): ?int
    {
        return $this->visitType;
    }

	/**
	 * @param int $visitType
	 *
	 * @return void
	 * @throws \Exception
	 */
    public function setVisitType(int $visitType): void
    {
        if (!array_key_exists($visitType, self::VISIT_TYPE) && null !== $visitType) {
            throw new \Exception('type visit  '.$visitType.' not authorised !');
        }
        $this->visitType = $visitType;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ArrayCollection<int, ReportModelVersion>
     */
    public function getVersions(): Collection
    {
        return $this->versions;
    }

    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    /**
     * @return $this
     */
    public function setDeletedAt(?DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getFieldsToBeIgnored(): array
    {
        return [];
    }

    /**
     * @return mixed
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @param mixed $publishedAt
     */
    public function setPublishedAt($publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

	/**
	 *  modification possible uniquement si le modèle n'a aucune version publiée
	 *
	 * @return bool
	 */
	public function hasPublishedVersion(): bool
	{
		$versions = $this->getVersions();
		foreach ($versions as $version) {
			if ($version::STATUS_PUBLISH === $version->getStatus()) {
				return true;
			}
		}

		return false;
    }

	/**
	 * création d'une nouvelle version possible uniquement si le modèle n'a aucune version en création
	 *
	 * @return bool
	 */
    public function canCreateVersion(): bool
	{
		$versions = $this->getVersions();
		$count = $versions->count();
		$arrayVersion = [];

		foreach ($versions as $version) {
			if ($version::STATUS_PUBLISH === $version->getStatus() || $version::STATUS_OBSOLETE === $version->getStatus()) {
				$arrayVersion[] = $version;
			}
		}

		if ($count != count($arrayVersion)) {
			return false;
		}

		return true;
	}
}
