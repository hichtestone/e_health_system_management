<?php

namespace App\ESM\Entity;

use App\ESM\Entity\DropdownList\DeviationSample\DecisionTaken;
use App\ESM\Entity\DropdownList\DeviationSample\DetectionCenter;
use App\ESM\Entity\DropdownList\DeviationSample\DetectionContext;
use App\ESM\Entity\DropdownList\DeviationSample\NatureType;
use App\ESM\Entity\DropdownList\DeviationSample\PotentialImpactSample;
use App\ESM\Entity\DropdownList\DeviationSample\ProcessInvolved;
use App\ESM\Repository\DeviationSampleRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DeviationSampleRepository::class)
 * @Vich\Uploadable
 */
class DeviationSample implements AuditrailableInterface
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
	 * @ORM\Column(type="string", length=120, nullable=false, unique=true)
	 *
	 * @var string
	 */
	private $code;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 *
	 * @var int
	 */
	private $grade;

	/**
	 * @ORM\Column(type="integer", nullable=false)
	 *
	 * @var int
	 */
	private $status;

	/**
	 * @var User|null
	 *
	 * @ORM\ManyToOne(targetEntity=User::class)
	 */
	private $createdBy;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * @Assert\LessThanOrEqual(
	 *     value="now",
	 *     message="La date de constat doit être inférieure ou égale de la date du jour"
	 * )
	 * @var DateTime|null
	 */
	private $observedAt;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * @Assert\LessThanOrEqual(
	 *     value="now",
	 *     message="La date d'occurence doit être inférieure ou égale de la date du jour"
	 * )
	 *
	 * @var DateTime|null
	 */
	private $occurrenceAt;

	/**
	 * @var DateTime
	 *
	 * @ORM\Column(type="datetime", nullable=false)
	 */
	private $declaredAt;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="deviationSamples")
	 * @ORM\JoinColumn(nullable=true)
	 *
	 * @var User|null
	 */
	private $declaredBy;

	/**
	 * @ORM\ManyToOne(targetEntity=DetectionContext::class)
	 * @ORM\JoinColumn(nullable=true)
	 *
	 * @var DetectionContext
	 */
	private $detectionContext;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 * @var string|null
	 */
	private $detectionContextReason;

	/**
	 * @ORM\ManyToOne(targetEntity=DetectionCenter::class)
	 * @ORM\JoinColumn(nullable=true)
	 *
	 * @var DetectionCenter
	 */
	private $detectionCenter;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 * @var string|null
	 */
	private $detectionCenterReason;

	/**
	 * @ORM\ManyToMany(targetEntity=ProcessInvolved::class, inversedBy="deviationSamples")
	 * @ORM\JoinTable(name="deviation_sample_process_involved")
	 *
	 * @var ArrayCollection<int, ProcessInvolved>
	 */
	private $processInvolves;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 * @var string|null
	 */
	private $processInvolvedReason;

	/**
	 * @ORM\ManyToOne(targetEntity=NatureType::class)
	 * @ORM\JoinColumn(nullable=true)
	 *
	 * @var NatureType
	 */
	private $natureType;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 * @var string|null
	 */
	private $natureTypeReason;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 *
	 * @var string|null
	 */
	private $resume;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 *
	 * @var string|null
	 */
	private $description;

	/**
	 * @ORM\Column(type="simple_array", nullable=true)
	 */
	private $causality;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 *
	 * @var string|null
	 */
	private $causalityDescription;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 *
	 * @var string|null
	 */
	private $causalityReason;

	/**
	 * @ORM\ManyToOne(targetEntity=PotentialImpactSample::class)
	 * @ORM\JoinColumn(nullable=true)
	 *
	 * @var PotentialImpactSample
	 */
	private $potentialImpactSample;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 * @var string|null
	 */
	private $potentialImpactSampleReason;

	/**
	 * @ORM\ManyToOne(targetEntity=DecisionTaken::class)
	 * @ORM\JoinColumn(nullable=true)
	 *
	 * @var DecisionTaken
	 */
	private $decisionTaken;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 * @var string|null
	 */
	private $decisionFile;

	/**
	 * @Vich\UploadableField(mapping="deviation_sample_decision_file", fileNameProperty="decisionFile")
	 *
	 * @var File|null
	 */
	private $decisionFileVich;

	/**
	 * Important : using by decisionFileVich
	 *
	 * @ORM\Column(type="datetime", nullable=true)
	 *
	 * @var DateTime|null
	 */
	private $updatedAt;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 *
	 *  @var DateTime|null
	 */
	private $decisionAt;

	/**
	 * @ORM\OneToMany(targetEntity=DeviationSampleAction::class, mappedBy="deviationSample")
	 *
	 * @var ArrayCollection<int, DeviationSampleAction>
	 */
	private $deviationSampleActions;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 *
	 * @var int|null
	 */
	private $efficiencyMeasure;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 *
	 * @var string|null
	 */
	private $efficiencyJustify;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 *
	 * @var string|null
	 */
	private $notEfficiencyMeasureReason;

	/**
	 * @ORM\OneToMany(targetEntity=DeviationSampleCorrection::class, mappedBy="deviationSample")
	 *
	 * @var ArrayCollection<int, DeviationSampleCorrection>
	 */
	private $deviationSampleCorrections;

	/**
	 * @ORM\ManyToMany(targetEntity=Project::class, inversedBy="deviationSamples")
	 * @ORM\JoinTable(name="deviation_sample_project")
	 *
	 * @var ArrayCollection<int, Project>
	 */
	private $projects;

	/**
	 * @ORM\ManyToMany(targetEntity=Institution::class, inversedBy="deviationSamples")
	 * @ORM\JoinTable(name="deviation_sample_institution")
	 *
	 * @var ArrayCollection<int, Institution>
	 */
	private $institutions;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 *
	 *  @var DateTime|null
	 */
	private $closedAt;

	/**
	 * @ORM\OneToMany(targetEntity=DeviationAndSample::class, mappedBy="deviationSample")
	 *
	 * @var ArrayCollection<int, DeviationAndSample>
	 */
	private $deviationAndSamples;

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * DeviationSample constructor.
	 */
	public function __construct()
	{
		$this->deviationSampleActions     = new ArrayCollection();
		$this->deviationSampleCorrections = new ArrayCollection();
		$this->institutions = new ArrayCollection();
		$this->projects = new ArrayCollection();
		$this->processInvolves = new ArrayCollection();
		$this->deviationAndSamples = new ArrayCollection();
	}

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->getCode();
	}

	/**
	 * @return string
	 */
	public function getCode(): string
	{
		return $this->code;
	}

	/**
	 * @param string $code
	 */
	public function setCode(string $code): void
	{
		$this->code = $code;
	}

	/**
	 * @return int|null
	 */
	public function getGrade(): ?int
	{
		return $this->grade;
	}

	/**
	 * @param int|null $grade
	 * @throws Exception
	 */
	public function setGrade(?int $grade): void
	{
		if (!array_key_exists($grade, Deviation::GRADES)) {
			throw new Exception('This grade : ' . $grade . ' does not exist!');
		}

		$this->grade = $grade;
	}

	/**
	 * @return int
	 */
	public function getStatus(): int
	{
		return $this->status;
	}

	/**
	 * @param int $status
	 * @throws Exception
	 */
	public function setStatus(int $status): void
	{
		if (!array_key_exists($status, Deviation::STATUS)) {
			throw new Exception('This status : ' . $status . ' does not exist!');
		}

		$this->status = $status;
	}

	/**
	 * @return User
	 */
	public function getCreatedBy(): ?User
	{
		return $this->createdBy;
	}

	/**
	 * @param User $createdBy
	 */
	public function setCreatedBy(User $createdBy): void
	{
		$this->createdBy = $createdBy;
	}

	/**
	 * @return DateTime|null
	 */
	public function getObservedAt(): ?DateTime
	{
		return $this->observedAt;
	}

	/**
	 * @param DateTime|null $observedAt
	 * @return $this
	 */
	public function setObservedAt(?DateTime $observedAt): self
	{
		$this->observedAt = $observedAt;

		return $this;
	}

	/**
	 * @return DateTime|null
	 */
	public function getOccurrenceAt(): ?DateTime
	{
		return $this->occurrenceAt;
	}

	/**
	 * @param DateTime|null $occurrenceAt
	 * @return $this
	 */
	public function setOccurrenceAt(?DateTime $occurrenceAt): self
	{
		$this->occurrenceAt = $occurrenceAt;

		return $this;
	}

	/**
	 * @return DateTime
	 */
	public function getDeclaredAt(): DateTime
	{
		return $this->declaredAt;
	}

	/**
	 * @param DateTime $declaredAt
	 * @return $this
	 */
	public function setDeclaredAt(DateTime $declaredAt): self
	{
		$this->declaredAt = $declaredAt;

		return $this;
	}

	/**
	 * @return User|null
	 */
	public function getDeclaredBy(): ?User
	{
		return $this->declaredBy;
	}

	/**
	 * @param User|null $declaredBy
	 */
	public function setDeclaredBy(?User $declaredBy): void
	{
		$this->declaredBy = $declaredBy;
	}

	/**
	 * @return DetectionContext|null
	 */
	public function getDetectionContext(): ?DetectionContext
	{
		return $this->detectionContext;
	}

	/**
	 * @param DetectionContext|null $detectionContext
	 */
	public function setDetectionContext(?DetectionContext $detectionContext): void
	{
		$this->detectionContext = $detectionContext;
	}

	/**
	 * @return string|null
	 */
	public function getDetectionContextReason(): ?string
	{
		return $this->detectionContextReason;
	}

	/**
	 * @param string|null $detectionContextReason
	 */
	public function setDetectionContextReason(?string $detectionContextReason): void
	{
		$this->detectionContextReason = $detectionContextReason;
	}

	/**
	 * @return DetectionCenter|null
	 */
	public function getDetectionCenter(): ?DetectionCenter
	{
		return $this->detectionCenter;
	}

	/**
	 * @param DetectionCenter|null $detectionCenter
	 */
	public function setDetectionCenter(?DetectionCenter $detectionCenter): void
	{
		$this->detectionCenter = $detectionCenter;
	}

	/**
	 * @return string|null
	 */
	public function getDetectionCenterReason(): ?string
	{
		return $this->detectionCenterReason;
	}

	/**
	 * @param string|null $detectionCenterReason
	 */
	public function setDetectionCenterReason(?string $detectionCenterReason): void
	{
		$this->detectionCenterReason = $detectionCenterReason;
	}

	/**
	 * @return Collection
	 */
	public function getProcessInvolves(): Collection
	{
		return $this->processInvolves;
	}

	/**
	 * @param ProcessInvolved $processInvolved
	 * @return $this
	 */
	public function addProcessInvolved(ProcessInvolved $processInvolved): self
	{
		if (!$this->processInvolves->contains($processInvolved)) {
			$this->processInvolves[] = $processInvolved;
		}

		return $this;
	}

	/**
	 * @param ProcessInvolved $processInvolved
	 * @return $this
	 */
	public function removeProcessInvolved(ProcessInvolved $processInvolved): self
	{
		if ($this->processInvolves->contains($processInvolved)) {
			$this->processInvolves->removeElement($processInvolved);
		}

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getProcessInvolvedReason(): ?string
	{
		return $this->processInvolvedReason;
	}

	/**
	 * @param string|null $processInvolvedReason
	 */
	public function setProcessInvolvedReason(?string $processInvolvedReason): void
	{
		$this->processInvolvedReason = $processInvolvedReason;

	}

	/**
	 * @return NatureType|null
	 */
	public function getNatureType(): ?NatureType
	{
		return $this->natureType;
	}

	/**
	 * @param NatureType|null $natureType
	 */
	public function setNatureType(?NatureType $natureType): void
	{
		$this->natureType = $natureType;
	}

	/**
	 * @return string|null
	 */
	public function getNatureTypeReason(): ?string
	{
		return $this->natureTypeReason;
	}

	/**
	 * @param string|null $natureTypeReason
	 */
	public function setNatureTypeReason(?string $natureTypeReason): void
	{
		$this->natureTypeReason = $natureTypeReason;
	}

	/**
	 * @return string|null
	 */
	public function getResume(): ?string
	{
		return $this->resume;
	}

	/**
	 * @param string|null $resume
	 * @return $this
	 */
	public function setResume(?string $resume): self
	{
		$this->resume = $resume;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @param string|null $description
	 */
	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	/**
	 * @return mixed
	 */
	public function getCausality()
	{
		return $this->causality;
	}

	/**
	 * @param mixed $causality
	 */
	public function setCausality($causality): void
	{
		$this->causality = $causality;
	}

	/**
	 * @return string|null
	 */
	public function getCausalityDescription(): ?string
	{
		return $this->causalityDescription;
	}

	/**
	 * @param string|null $causalityDescription
	 */
	public function setCausalityDescription(?string $causalityDescription): void
	{
		$this->causalityDescription = $causalityDescription;
	}

	/**
	 * @return string|null
	 */
	public function getCausalityReason(): ?string
	{
		return $this->causalityReason;
	}

	/**
	 * @param string|null $causalityReason
	 */
	public function setCausalityReason(?string $causalityReason): void
	{
		$this->causalityReason = $causalityReason;
	}

	/**
	 * @return PotentialImpactSample|null
	 */
	public function getPotentialImpactSample(): ?PotentialImpactSample
	{
		return $this->potentialImpactSample;
	}

	/**
	 * @param PotentialImpactSample|null $potentialImpactSample
	 */
	public function setPotentialImpactSample(?PotentialImpactSample $potentialImpactSample): void
	{
		$this->potentialImpactSample = $potentialImpactSample;
	}

	/**
	 * @return string|null
	 */
	public function getPotentialImpactSampleReason(): ?string
	{
		return $this->potentialImpactSampleReason;
	}

	/**
	 * @param string|null $potentialImpactSampleReason
	 */
	public function setPotentialImpactSampleReason(?string $potentialImpactSampleReason): void
	{
		$this->potentialImpactSample = $potentialImpactSampleReason;
	}

	/**
	 * @return DecisionTaken|null
	 */
	public function getDecisionTaken(): ?DecisionTaken
	{
		return $this->decisionTaken;
	}

	/**
	 * @param DecisionTaken|null $decisionTaken
	 */
	public function setDecisionTaken(?DecisionTaken $decisionTaken): void
	{
		$this->decisionTaken = $decisionTaken;
	}

	/**
	 * @return string|null
	 */
	public function getDecisionFile(): ?string
	{
		return $this->decisionFile;
	}

	/**
	 * @param string|null $decisionFile
	 */
	public function setDecisionFile(?string $decisionFile): void
	{
		$this->decisionFile = $decisionFile;
	}

	/**
	 * @return File|null
	 */
	public function getDecisionFileVich(): ?File
	{
		return $this->decisionFileVich;
	}

	/**
	 * @param File|null $decisionFileVich
	 * @return $this
	 */
	public function setDecisionFileVich(?File $decisionFileVich): DeviationSample
	{
		$this->decisionFileVich = $decisionFileVich;
		if ($this->decisionFileVich instanceof UploadedFile) {
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
	 * @return DateTime|null
	 */
	public function getDecisionAt(): ?DateTime
	{
		return $this->decisionAt;
	}

	/**
	 * @param DateTime|null $decisionAt
	 * @return $this
	 */
	public function setDecisionAt(?DateTime $decisionAt): self
	{
		$this->decisionAt = $decisionAt;

		return $this;
	}

	/**
	 * @return Collection|DeviationSampleAction[]
	 */
	public function getDeviationSampleActions(): Collection
	{
		return $this->deviationSampleActions;
	}

	/**
	 * @param DeviationSampleAction $deviationSampleAction
	 * @return $this
	 */
	public function addDeviationSampleAction(DeviationSampleAction $deviationSampleAction): self
	{
		if (!$this->deviationSampleActions->contains($deviationSampleAction)) {
			$this->deviationSampleActions[] = $deviationSampleAction;
			$deviationSampleAction->setDeviationSample($this);
		}

		return $this;
	}

	/**
	 * @param DeviationSampleAction $deviationSampleAction
	 * @return $this
	 */
	public function removeDeviationSampleAction(DeviationSampleAction $deviationSampleAction): self
	{
		if ($this->deviationSampleActions->removeElement($deviationSampleAction)) {
			// set the owning side to null (unless already changed)
			if ($deviationSampleAction->getDeviationSample() === $this) {
				$deviationSampleAction->setDeviationSample(null);
			}
		}

		return $this;
	}

	/**
	 * @return int|null
	 */
	public function getEfficiencyMeasure(): ?int
	{
		return $this->efficiencyMeasure;
	}

	/**
	 * @param int|null $efficiencyMeasure
	 *
	 * @throws Exception
	 */
	public function setEfficiencyMeasure(?int $efficiencyMeasure): void
	{
		if (!array_key_exists($efficiencyMeasure, Deviation::EFFICIENCY_MEASURE)) {
			throw new Exception('the efficiency measure  ' . $efficiencyMeasure . ' is not authorize !');
		}

		$this->efficiencyMeasure = $efficiencyMeasure;
	}

	/**
	 * @return string|null
	 */
	public function getEfficiencyJustify(): ?string
	{
		return $this->efficiencyJustify;
	}

	/**
	 * @param string|null $efficiencyJustify
	 */
	public function setEfficiencyJustify(?string $efficiencyJustify): void
	{
		$this->efficiencyJustify = $efficiencyJustify;
	}

	/**
	 * @return string|null
	 */
	public function getNotEfficiencyMeasureReason(): ?string
	{
		return $this->notEfficiencyMeasureReason;
	}

	/**
	 * @param string|null $notEfficiencyMeasureReason
	 */
	public function setNotEfficiencyMeasureReason(?string $notEfficiencyMeasureReason): void
	{
		$this->notEfficiencyMeasureReason = $notEfficiencyMeasureReason;
	}

	/**
	 * @return Collection
	 */
	public function getDeviationSampleCorrections(): Collection
	{
		return $this->deviationSampleCorrections;
	}

	/**
	 * @param DeviationSampleCorrection $deviationSampleCorrection
	 * @return $this
	 */
	public function addDeviationSampleCorrection(DeviationSampleCorrection $deviationSampleCorrection): self
	{
		if (!$this->deviationSampleCorrections->contains($deviationSampleCorrection)) {
			$this->deviationSampleCorrections[] = $deviationSampleCorrection;
			$deviationSampleCorrection->setDeviationSample($this);
		}

		return $this;
	}

	/**
	 * @param DeviationSampleCorrection $deviationSampleCorrection
	 * @return $this
	 */
	public function removeDeviationSampleCorrection(DeviationSampleCorrection $deviationSampleCorrection): self
	{
		if ($this->deviationSampleCorrections->removeElement($deviationSampleCorrection)) {
			// set the owning side to null (unless already changed)
			if ($deviationSampleCorrection->getDeviationSample() === $this) {
				$deviationSampleCorrection->setDeviationSample(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getProjects(): Collection
	{
		return $this->projects;
	}

	/**
	 * @param Project $project
	 * @return $this
	 */
	public function addProject(Project $project): self
	{
		if (!$this->projects->contains($project)) {
			$this->projects[] = $project;
		}

		return $this;
	}

	/**
	 * @param Project $project
	 * @return $this
	 */
	public function removeProject(Project $project): self
	{
		if ($this->projects->contains($project)) {
			$this->projects->removeElement($project);
		}

		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getInstitutions(): Collection
	{
		return $this->institutions;
	}

	/**
	 * @param Institution $institution
	 * @return $this
	 */
	public function addInstitution(Institution $institution): self
	{
		if (!$this->institutions->contains($institution)) {
			$this->institutions[] = $institution;
		}

		return $this;
	}

	/**
	 * @param Institution $institution
	 * @return $this
	 */
	public function removeInstitution(Institution $institution): self
	{
		if ($this->institutions->contains($institution)) {
			$this->institutions->removeElement($institution);
		}

		return $this;
	}

	/**
	 * @return DateTime|null
	 */
	public function getClosedAt(): ?DateTime
	{
		return $this->closedAt;
	}

	/**
	 * @param DateTime|null $closedAt
	 * @return $this
	 */
	public function setClosedAt(?DateTime $closedAt): self
	{
		$this->closedAt = $closedAt;

		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getDeviationAndSamples(): Collection
	{
		return $this->deviationAndSamples;
	}

	/**
	 * @param DeviationAndSample $deviationAndSample
	 * @return $this
	 */
	public function addDeviationAndSample(DeviationAndSample $deviationAndSample): self
	{
		if (!$this->deviationAndSamples->contains($deviationAndSample)) {
			$this->deviationAndSamples[] = $deviationAndSample;
			$deviationAndSample->setDeviationSample($this);
		}

		return $this;
	}

	/**
	 * @param DeviationAndSample $deviationAndSample
	 * @return $this
	 */
	public function removeDeviationAndSample(DeviationAndSample $deviationAndSample): self
	{
		if ($this->deviationAndSamples->removeElement($deviationAndSample)) {
			// set the owning side to null (unless already changed)
			if ($deviationAndSample->getDeviationSample() === $this) {
				$deviationAndSample->setDeviationSample(null);
			}
		}

		return $this;
	}

	public function getFieldsToBeIgnored(): array
	{
		return [];
	}

	/**
	 * @return bool
	 */
	public function hasAction(): bool
	{
		$hasAction = false;

		if (!$this->deviationSampleActions->isEmpty()) {
			$hasAction = true;
		}

		return $hasAction;
	}

	/**
	 * @return bool
	 */
	public function hasLeastOneActionWithoutRealizedDate(): bool
	{
		$hasRealizedAt = false;

		$actions = $this->deviationSampleActions;

		foreach ($actions as $action) {
			if (!$action->getDoneAt()) {
				$hasRealizedAt = true;
			}
		}

		return $hasRealizedAt;
	}

	/**
	 * @return bool
	 */
	public function hasMeasureOfEfficiencyEmpty(): bool
	{
		return !(null !== $this->getEfficiencyMeasure());
	}

}
