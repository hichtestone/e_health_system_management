<?php

namespace App\ESM\Entity;

use App\ESM\Entity\DropdownList\DeviationSystem\ProcessSystem;
use App\ESM\Repository\DeviationSystemRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DeviationSystemRepository::class)
 */
class DeviationSystem implements AuditrailableInterface
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=120, nullable=false, unique=true)
	 */
	private $code;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 *
	 * @var int|null
	 */
	private $grade;

	/**
	 * @ORM\ManyToMany(targetEntity=ProcessSystem::class, inversedBy="deviationsSystem")
	 * @ORM\JoinTable(name="deviation_process_system")
	 *
	 * @var ArrayCollection<int, ProcessSystem>
	 */
	private $process;

	/**
	 * @ORM\Column(type="simple_array", nullable=true)
	 */
	private $causality;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $causalityDescription;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $potentialImpact;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $potentialImpactDescription;

	/**
	 * @ORM\Column(type="smallint", nullable=true)
	 *
	 * @var int|null
	 */
	private $status;

	/**
	 * @ORM\Column(type="text", nullable=true)
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
	 * @ORM\Column(type="datetime", nullable=true)
	 * @Assert\LessThanOrEqual(
	 *     value="now",
	 *     message="La date de constat doit être inférieure ou égale de la date du jour"
	 * )
	 */
	private $observedAt;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $declaredAt;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="deviations")
	 */
	private $declaredBy;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $closedAt;

	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 *
	 * @var bool|null
	 */
	private $isCrexSubmission;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="deviationsSystem")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $referentQA;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 *
	 * @var string|null
	 */
	private $activity;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 *
	 * @var string|null
	 */
	private $refISO9001;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 *
	 * @var string|null
	 */
	private $document;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 *
	 * @var string|null
	 */
	private $visaPilotProcessChiefQA;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $visaAt;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="deviationsSystemOfficial")
	 */
	private $officialQA;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $efficiencyMeasure;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $efficiencyJustify;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $notEfficiencyMeasureReason;

	/**
	 * @ORM\OneToMany(targetEntity=DeviationSystemCorrection::class, mappedBy="deviationSystem")
	 *
	 * @var ArrayCollection<int, DeviationSystemCorrection>
	 */
	private $corrections;

	/**
	 * @ORM\OneToMany(targetEntity=DeviationSystemAction::class, mappedBy="deviationSystem")
	 *
	 * @var ArrayCollection<int, DeviationSystemAction>
	 */
	private $actions;

	/**
	 * @ORM\OneToMany(targetEntity=DeviationSystemReview::class, mappedBy="deviationSystem")
	 *
	 * @var ArrayCollection<int, DeviationSystemReview>
	 */
	private $reviews;

	/**
	 * DeviationSystem constructor.
	 */
	public function __construct()
	{
		$this->corrections 	= new ArrayCollection();
		$this->process 	   	= new ArrayCollection();
		$this->reviews 		= new ArrayCollection();
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	public function __toString(): string
	{
		return $this->getCode();
	}

	/**
	 * @param mixed $id
	 */
	public function setId($id): void
	{
		$this->id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @param mixed $code
	 */
	public function setCode($code): void
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
	 */
	public function setGrade(?int $grade): void
	{
		$this->grade = $grade;
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
	 * @return mixed
	 */
	public function getCausalityDescription()
	{
		return $this->causalityDescription;
	}

	/**
	 * @param mixed $causalityDescription
	 */
	public function setCausalityDescription($causalityDescription): void
	{
		$this->causalityDescription = $causalityDescription;
	}

	/**
	 * @return mixed
	 */
	public function getPotentialImpact()
	{
		return $this->potentialImpact;
	}

	/**
	 * @param mixed $potentialImpact
	 */
	public function setPotentialImpact($potentialImpact): void
	{
		$this->potentialImpact = $potentialImpact;
	}

	/**
	 * @return mixed
	 */
	public function getPotentialImpactDescription()
	{
		return $this->potentialImpactDescription;
	}

	/**
	 * @param mixed $potentialImpactDescription
	 */
	public function setPotentialImpactDescription($potentialImpactDescription): void
	{
		$this->potentialImpactDescription = $potentialImpactDescription;
	}

	/**
	 * @return int|null
	 */
	public function getStatus(): ?int
	{
		return $this->status;
	}

	/**
	 * @param int|null $status
	 */
	public function setStatus(?int $status): void
	{
		$this->status = $status;
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
	 */
	public function setResume(?string $resume): void
	{
		$this->resume = $resume;
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
	public function getObservedAt()
	{
		return $this->observedAt;
	}

	/**
	 * @param mixed $observedAt
	 */
	public function setObservedAt($observedAt): void
	{
		$this->observedAt = $observedAt;
	}

	/**
	 * @return mixed
	 */
	public function getDeclaredAt()
	{
		return $this->declaredAt;
	}

	/**
	 * @param mixed $declaredAt
	 */
	public function setDeclaredAt($declaredAt): void
	{
		$this->declaredAt = $declaredAt;
	}

	/**
	 * @return mixed
	 */
	public function getDeclaredBy()
	{
		return $this->declaredBy;
	}

	/**
	 * @param mixed $declaredBy
	 */
	public function setDeclaredBy($declaredBy): void
	{
		$this->declaredBy = $declaredBy;
	}

	/**
	 * @return mixed
	 */
	public function getClosedAt()
	{
		return $this->closedAt;
	}

	/**
	 * @param mixed $closedAt
	 */
	public function setClosedAt($closedAt): void
	{
		$this->closedAt = $closedAt;
	}

	/**
	 * @return bool
	 */
	public function getIsCrexSubmission() : bool
	{
		return $this->isCrexSubmission;
	}

	/**
	 * @param bool $isCrexSubmission
	 * @return $this
	 */
	public function setIsCrexSubmission(bool $isCrexSubmission): self
	{
		$this->isCrexSubmission = $isCrexSubmission;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getReferentQA()
	{
		return $this->referentQA;
	}

	/**
	 * @param mixed $referentQA
	 */
	public function setReferentQA($referentQA): void
	{
		$this->referentQA = $referentQA;
	}

	/**
	 * @return string
	 */
	public function getEfficiencyMeasure(): ?string
	{
		return $this->efficiencyMeasure;
	}

	/**
	 * @param string|null $efficiencyMeasure
	 *
	 * @throws Exception
	 */
	public function setEfficiencyMeasure(?string $efficiencyMeasure): void
	{
		if (!array_key_exists($efficiencyMeasure, Deviation::EFFICIENCY_MEASURE) && $efficiencyMeasure) {
			throw new Exception('the efficiency measure  '.$efficiencyMeasure.' is not authorize !');
		}

		$this->efficiencyMeasure = $efficiencyMeasure;
	}

	/**
	 * @return mixed
	 */
	public function getEfficiencyJustify()
	{
		return $this->efficiencyJustify;
	}

	/**
	 * @param mixed $efficiencyJustify
	 */
	public function setEfficiencyJustify($efficiencyJustify): void
	{
		$this->efficiencyJustify = $efficiencyJustify;
	}

	/**
	 * @return mixed
	 */
	public function getNotEfficiencyMeasureReason()
	{
		return $this->notEfficiencyMeasureReason;
	}

	/**
	 * @param mixed $notEfficiencyMeasureReason
	 */
	public function setNotEfficiencyMeasureReason($notEfficiencyMeasureReason): void
	{
		$this->notEfficiencyMeasureReason = $notEfficiencyMeasureReason;
	}

	/**
	 * @return string|null
	 */
	public function getActivity(): ?string
	{
		return $this->activity;
	}

	/**
	 * @param string|null $activity
	 */
	public function setActivity(?string $activity): void
	{
		$this->activity = $activity;
	}

	/**
	 * @return string|null
	 */
	public function getRefISO9001(): ?string
	{
		return $this->refISO9001;
	}

	/**
	 * @param string|null $refISO9001
	 */
	public function setRefISO9001(?string $refISO9001): void
	{
		$this->refISO9001 = $refISO9001;
	}

	/**
	 * @return string|null
	 */
	public function getDocument(): ?string
	{
		return $this->document;
	}

	/**
	 * @param string|null $document
	 */
	public function setDocument(?string $document): void
	{
		$this->document = $document;
	}

	/**
	 * @return string|null
	 */
	public function getVisaPilotProcessChiefQA(): ?string
	{
		return $this->visaPilotProcessChiefQA;
	}

	/**
	 * @param string|null $visaPilotProcessChiefQA
	 */
	public function setVisaPilotProcessChiefQA(?string $visaPilotProcessChiefQA): void
	{
		$this->visaPilotProcessChiefQA = $visaPilotProcessChiefQA;
	}

	/**
	 * @return mixed
	 */
	public function getVisaAt()
	{
		return $this->visaAt;
	}

	/**
	 * @param mixed $visaAt
	 */
	public function setVisaAt($visaAt): void
	{
		$this->visaAt = $visaAt;
	}

	/**
	 * @return mixed
	 */
	public function getOfficialQA()
	{
		return $this->officialQA;
	}

	/**
	 * @param mixed $officialQA
	 */
	public function setOfficialQA($officialQA): void
	{
		$this->officialQA = $officialQA;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getCorrections(): ArrayCollection
	{
		return $this->corrections;
	}

	/**
	 * @param DeviationSystemCorrection $correction
	 * @return $this
	 */
	public function addCorrection(DeviationSystemCorrection $correction): self
	{
		if (!$this->corrections->contains($correction)) {
			$this->corrections[] = $correction;
		}

		return $this;
	}

	/**
	 * @param DeviationSystemCorrection $correction
	 * @return $this
	 */
	public function removeCorrection(DeviationSystemCorrection $correction): self
	{
		if ($this->corrections->contains($correction)) {
			$this->corrections->removeElement($correction);
		}

		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getProcess(): Collection
	{
		return $this->process;
	}

	/**
	 * @param ProcessSystem $process
	 * @return $this
	 */
	public function addProcess(ProcessSystem $process): self
	{
		if (!$this->process->contains($process)) {
			$this->process[] = $process;
		}

		return $this;
	}

	/**
	 * @param ProcessSystem $process
	 * @return $this
	 */
	public function removeProcess(ProcessSystem $process): self
	{
		if ($this->process->contains($process)) {
			$this->process->removeElement($process);
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	public function removeAllProcess(): self
	{
		$this->process = new ArrayCollection();

		return $this;
	}

	/**
	 * @return Collection|DeviationSystemAction[]
	 */
	public function getActions(): Collection
	{
		return $this->actions;
	}

	/**
	 * @param DeviationSystemAction $action
	 * @return $this
	 */
	public function addAction(DeviationSystemAction $action): self
	{
		if (!$this->actions->contains($action)) {
			$this->actions[] = $action;
			$action->setDeviationSystem($this);
		}

		return $this;
	}

	/**
	 * @param DeviationSystemAction $action
	 * @return $this
	 */
	public function removeAction(DeviationSystemAction $action): self
	{
		if ($this->actions->removeElement($action)) {
			// set the owning side to null (unless already changed)
			if ($action->getDeviationSystem() === $this) {
				$action->setDeviationSystem(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection|DeviationSystemReview[]
	 */
	public function getReviews(): Collection
	{
		return $this->reviews;
	}

	/**
	 * @return $this
	 */
	public function addReview(DeviationSystemReview $review): self
	{
		if (!$this->reviews->contains($review)) {
			$this->reviews[] = $review;
			$review->setDeviationSystem($this);
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	public function removeReview(DeviationSystemReview $review): self
	{
		if ($this->reviews->removeElement($review)) {
			// set the owning side to null (unless already changed)
			if ($review->getDeviationSystem() === $this) {
				$review->setDeviationSystem(null);
			}
		}

		return $this;
	}

	/**
	 * @return array
	 */
	public function getFieldsToBeIgnored(): array
	{
		return [];
	}

	/**
	 * @return bool
	 * @throws Exception
	 */
	public function isClosable(): bool
	{
		if (!$this->hasOneLeastFinishedReview() || !$this->hasAction() || !$this->hasReview() || $this->hasLeastOneActionWithoutRealizedDate() || $this->hasMeasureOfEfficiencyEmpty()) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * @return bool
	 */
	public function hasOneLeastFinishedReview(): bool
	{
		$reviews = $this->getReviews();

		if (!$reviews->isEmpty()) {
			foreach ($reviews as $review) {
				if ($review->getStatus() === DeviationReview::STATUS_FINISH) {
					return true;
				}
			}
		} else {
			return false;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function hasAction(): bool
	{
		if ($this->actions->isEmpty()) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * @return bool
	 */
	public function hasReview(): bool
	{
		if ($this->reviews->isEmpty()) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * @return bool
	 */
	public function hasLeastOneActionWithoutRealizedDate(): bool
	{
		$result = false;

		if ($this->actions) {
			foreach ($this->actions as $action) {
				if (null === $action->getDeletedAt() && null === $action->getDoneAt()) {
					$result =  true;
				}
			}
		}

		return $result;
	}

	/**
	 * @return bool
	 * @throws Exception
	 */
	public function hasMeasureOfEfficiencyEmpty(): bool
	{
		if ($this->getEfficiencyMeasure() === '0') {
			$this->setEfficiencyMeasure(0);
		} elseif ($this->getEfficiencyMeasure() === '1') {
			$this->setEfficiencyMeasure(1);
		} elseif ($this->getEfficiencyMeasure() === '2') {
			$this->setEfficiencyMeasure(2);
		}

		return ($this->getEfficiencyMeasure() !== '0' && $this->getEfficiencyMeasure() !== '1' && $this->getEfficiencyMeasure() !== '2');
	}
}
