<?php

namespace App\ESM\Entity;

use App\ESM\Repository\ReportVisitRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ReportVisitRepository::class)
 * @Vich\Uploadable
 */
class ReportVisit implements AuditrailableInterface
{
	public const VISIT_ON_SITE = 0;
	public const VISIT_OFF_SITE = 1;
	public const VISIT_TYPE = [
		self::VISIT_ON_SITE => 'On site',
		self::VISIT_OFF_SITE => 'Off site',
	];

	public const VISIT_STATUS_DONE = 0;
	public const VISIT_STATUS_NO_DONE = 1;
	public const VISIT_STATUS = [
		self::VISIT_STATUS_DONE => 'Faite',
		self::VISIT_STATUS_NO_DONE => 'Non-faite',
	];

	public const REPORT_IN_FOLLOW_UP = 0;
	public const REPORT_PROD = 1;
	public const REPORT_CLOS = 2;
	public const REPORT_TYPE = [
		self::REPORT_IN_FOLLOW_UP => 'Monitoring',
		self::REPORT_PROD => 'Mise en place',
		self::REPORT_CLOS => 'Clôture',
	];

	public const REPORT_STATUS_IN_PROGRESS = 0;
	public const REPORT_STATUS_VALIDATE = 1;
	public const REPORT_STATUS = [
		self::REPORT_STATUS_IN_PROGRESS => 'En rédaction / Correction',
		self::REPORT_STATUS_VALIDATE => 'Validé',
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
	private $visitType;

	/**
	 * @ORM\Column(type="integer", nullable=false)
	 */
	private $numberVisit;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $visitStatus;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 *
	 * @var int|null
	 */
	private $reportType;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $reportStatus;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 *
	 * @var DateTime
	 */
	private $expectedAt;

	/**
	 * @var User|null
	 *
	 * @Gedmo\Blameable(on="create")
	 * @ORM\ManyToOne(targetEntity=User::class)
	 */
	private $reportedBy;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 *
	 * @var DateTime|null
	 */
	private $reportedAt;

	/**
	 * @var User|null
	 *
	 * @Gedmo\Blameable(on="change", field="reportStatus", value="1")
	 * @ORM\ManyToOne(targetEntity=User::class)
	 * @ORM\JoinColumn(name="validated_by", referencedColumnName="id")
	 */
	private $validatedBy;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 *
	 * @var DateTime|null
	 */
	private $validatedAt;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 * @var string|null
	 */
	private $code;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 * @var string|null
	 */
	private $reportFile;

	/**
	 * @Vich\UploadableField(mapping="report_end_file", fileNameProperty="reportFile")
	 *
	 * @var File|null
	 */
	private $reportFileVich;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $updatedAt;

	/**
	 * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="reportVisits")
	 * @ORM\JoinColumn(nullable=false)
	 *
	 * @var Project
	 */
	private $project;

	/**
	 * @ORM\ManyToOne(targetEntity=Center::class, inversedBy="reportVisits")
	 * @ORM\JoinColumn(nullable=false)
	 *
	 * @var Center
	 */
	private $center;

	/**
	 * @ORM\ManyToOne(targetEntity=ReportConfigVersion::class, inversedBy="reportVisits")
	 * @ORM\JoinColumn(nullable=true)
	 *
	 * @var ReportConfigVersion
	 */
	private $reportConfigVersion;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 *
	 * @var DateTime|null
	 */
	private $deletedAt;

	public function __toString(): string
	{
		return $this->getNumberVisit();
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return array
	 */
	public function getFieldsToBeIgnored(): array
	{
		return [ 'project', 'center' ];
	}

	/**
	 * @return int|null
	 */
	public function getVisitStatus(): ?int
	{
		return $this->visitStatus;
	}

	/**
	 * @return int|null
	 */
	public function getReportType(): ?int
	{
		return $this->reportType;
	}

	/**
	 * @param int|null $reportType
	 * @throws Exception
	 */
	public function setReportType(?int $reportType): void
	{
		if (!array_key_exists($reportType, self::REPORT_TYPE) && null !== $reportType) {
			throw new Exception('type report  ' . $reportType . ' not authorised !');
		}
		$this->reportType = $reportType;
	}

	/**
	 * @param int|null $visitStatus
	 * @throws Exception
	 */
	public function setVisitStatus(?int $visitStatus): void
	{
		if (!array_key_exists($visitStatus, self::VISIT_STATUS) && null !== $visitStatus) {
			throw new Exception('status  ' . $visitStatus . ' not authorised !');
		}
		$this->visitStatus = $visitStatus;
	}

	/**
	 * @return int|null
	 */
	public function getReportStatus(): ?int
	{
		return $this->reportStatus;
	}

	/**
	 * @param int|null $reportStatus
	 * @throws Exception
	 */
	public function setReportStatus(?int $reportStatus): void
	{
		if (!array_key_exists($reportStatus, self::REPORT_STATUS) && null !== $reportStatus) {
			throw new Exception('status  ' . $reportStatus . ' not authorised !');
		}
		$this->reportStatus = $reportStatus;
	}

	public function getVisitType(): ?int
	{
		return $this->visitType;
	}

	/**
	 * @param $visitType
	 * @throws Exception
	 */
	public function setVisitType($visitType): void
	{
		if (!array_key_exists($visitType, self::VISIT_TYPE) && null !== $visitType) {
			throw new Exception('type visit  ' . $visitType . ' not authorised !');
		}
		$this->visitType = $visitType;
	}

	/**
	 * @return int|null
	 */
	public function getNumberVisit(): ?int
	{
		return $this->numberVisit;
	}

	/**
	 * @param string $numberVisit
	 * @return $this
	 */
	public function setNumberVisit(string $numberVisit): self
	{
		$this->numberVisit = $numberVisit;

		return $this;
	}

	/**
	 * @return DateTime
	 */
	public function getExpectedAt()
	{
		return $this->expectedAt;
	}

	/**
	 * @param DateTime|null $expectedAt
	 * @return $this
	 */
	public function setExpectedAt(?DateTime $expectedAt): self
	{
		$this->expectedAt = $expectedAt;

		return $this;
	}

	/**
	 * @return User|null
	 */
	public function getReportedBy(): ?User
	{
		return $this->reportedBy;
	}

	/**
	 * @param User|null $reportedBy
	 */
	public function setReportedBy(?User $reportedBy): void
	{
		$this->reportedBy = $reportedBy;
	}

	/**
	 * @return DateTime|null
	 */
	public function getReportedAt()
	{
		return $this->reportedAt;
	}

	/**
	 * @param DateTime|null $reportedAt
	 * @return $this
	 */
	public function setReportedAt(?DateTime $reportedAt): self
	{
		$this->reportedAt = $reportedAt;

		return $this;
	}

	/**
	 * @return User|null
	 */
	public function getValidatedBy(): ?User
	{
		return $this->validatedBy;
	}

	/**
	 * @param User|null $validatedBy
	 */
	public function setValidatedBy(?User $validatedBy): void
	{
		$this->validatedBy = $validatedBy;
	}

	/**
	 * @return DateTime|null
	 */
	public function getValidatedAt(): ?DateTime
	{
		return $this->validatedAt;
	}

	/**
	 * @return $this
	 */
	public function setValidatedAt(?DateTime $validatedAt): self
	{
		$this->validatedAt = $validatedAt;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getCode(): ?string
	{
		return $this->code;
	}

	/**
	 * @param string|null $code
	 * @return $this
	 */
	public function setCode(?string $code): self
	{
		$this->code = $code;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getReportFile(): ?string
	{
		return $this->reportFile;
	}

	/**
	 * @param string|null $reportFile
	 */
	public function setReportFile(?string $reportFile): void
	{
		$this->reportFile = $reportFile;
	}

	/**
	 * @return File|null
	 */
	public function getReportFileVich(): ?File
	{
		return $this->reportFileVich;
	}

	/**
	 * @param File|null $reportFileVich
	 * @return $this
	 */
	public function setReportFileVich(?File $reportFileVich): ReportVisit
	{
		$this->reportFileVich = $reportFileVich;
		if ($this->reportFileVich instanceof UploadedFile) {
			$this->updatedAt = new DateTime();
		}

		return $this;
	}

	/**
	 * @return DateTime|null
	 */
	public function getUpdatedAt(): ?DateTime
	{
		return $this->updatedAt;
	}

	/**
	 * @param DateTime $updatedAt
	 * @return $this
	 */
	public function setUpdatedAt(DateTime $updatedAt): self
	{
		$this->updatedAt = $updatedAt;

		return $this;
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
	 * @return Center|null
	 */
	public function getCenter(): ?Center
	{
		return $this->center;
	}

	/**
	 * @param Center $center
	 */
	public function setCenter(Center $center): void
	{
		$this->center = $center;
	}

	/**
	 * @return ReportConfigVersion|null
	 */
	public function getReportConfigVersion(): ?ReportConfigVersion
	{
		return $this->reportConfigVersion;
	}

	/**
	 * @param ReportConfigVersion|null $reportConfigVersion
	 */
	public function setReportConfigVersion(?ReportConfigVersion $reportConfigVersion): void
	{
		$this->reportConfigVersion = $reportConfigVersion;
	}

	/**
	 * @return string|null
	 */
	public function getModelReportType(): ?string
	{
		return (null !== $this->getReportConfigVersion()) ? $this->getReportConfigVersion()->getModelType() : null;
	}

	/**
	 * @return DateTime|null
	 */
	public function getDeletedAt(): ?DateTime
	{
		return $this->deletedAt;
	}

	/**
	 * @param DateTime|null $deletedAt
	 * @return $this
	 */
	public function setDeletedAt(?DateTime $deletedAt): self
	{
		$this->deletedAt = $deletedAt;

		return $this;
	}
}
