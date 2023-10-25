<?php

namespace App\ESM\Entity;

use App\ESM\Entity\DropdownList\DeviationType;
use App\ESM\Repository\DeviationRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DeviationRepository::class)
 */
class Deviation implements AuditrailableInterface
{
    public const STATUS_DRAFT       = 0;
    public const STATUS_IN_PROGRESS = 1;
    public const STATUS_DONE        = 2;
    public const STATUS = [
        self::STATUS_DRAFT       => 'Brouillon',
        self::STATUS_IN_PROGRESS => 'En cours',
        self::STATUS_DONE        => 'Terminée',
    ];

    public const CAUSALITY_HUMAN = 0;
    public const CAUSALITY_EQUIPMENT = 1;
    public const CAUSALITY_OTHER = 2;
    public const CAUSALITY = [
        self::CAUSALITY_HUMAN       => 'Humain',
        self::CAUSALITY_EQUIPMENT   => 'Equipement',
        self::CAUSALITY_OTHER       => 'Autre',
    ];

    public const GRADE_MINEUR = 1;
    public const GRADE_MAJEUR = 2;
    public const GRADE_CRITIQUE = 3;
    public const GRADES = [
        self::GRADE_MINEUR      => 'Mineur',
        self::GRADE_MAJEUR      => 'Mjeur',
        self::GRADE_CRITIQUE    => 'Critique',
    ];

    public const POTENTIAL_IMPACT_IMP = 1;
    public const POTENTIAL_IMPACT_PATIENT_CONFIDENTIALITY   = 2;
    public const POTENTIAL_IMPACT_PATIENT_SAFETY            = 3;
    public const POTENTIAL_IMPACT_SCIENTIFIC_VALUE_DATA     = 4;
    public const POTENTIAL_IMPACT_CREDIBILITY               = 5;
    public const POTENTIAL_IMPACT_OTHERS                    = 99;
    public const POTENTIAL_IMPACT = [
        self::POTENTIAL_IMPACT_IMP                      => 'IMP',
        self::POTENTIAL_IMPACT_PATIENT_CONFIDENTIALITY  => 'Patient Confidentiality',
        self::POTENTIAL_IMPACT_PATIENT_SAFETY           => 'Patient Safety',
        self::POTENTIAL_IMPACT_SCIENTIFIC_VALUE_DATA    => 'Scientific Value Data',
        self::POTENTIAL_IMPACT_CREDIBILITY              => 'Credibility',
        self::POTENTIAL_IMPACT_OTHERS                   => 'Others',
    ];

    public const EFFICIENCY_MEASURE_EFFICIENT           = 'efficace';
    public const EFFICIENCY_MEASURE_INEFFICIENT         = 'peu efficace';
    public const EFFICIENCY_MEASURE_NOT_EFFICIENT       = 'pas efficace';

    public const EFFICIENCY_MEASURE = [
        self::EFFICIENCY_MEASURE_EFFICIENT,
        self::EFFICIENCY_MEASURE_INEFFICIENT,
        self::EFFICIENCY_MEASURE_NOT_EFFICIENT,
    ];

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
     * @ORM\ManyToOne(targetEntity=DeviationType::class, inversedBy="deviations")
     * @ORM\JoinColumn(nullable=true)
     *
     * @var DeviationType
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=DeviationType::class, inversedBy="deviationsSubType")
     * @ORM\JoinColumn(nullable=true)
     *
     * @var DeviationType
     */
    private $subType;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="deviations")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Project
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity=Center::class, inversedBy="deviations")
     * @ORM\JoinColumn(nullable=true)
     *
     * @var Center
     */
    private $center;

    /**
     * @ORM\ManyToOne(targetEntity=Institution::class, inversedBy="deviations")
     * @ORM\JoinColumn(nullable=true)
     *
     * @var Institution
     */
    private $institution;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="deviations")
     * @ORM\JoinColumn(nullable=true)
     *
     * @var Patient
     */
    private $patient;

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
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int|null
     */
    private $grade;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     *
     * @var int|null
     */
    private $status;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $causality;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $causalityDescription;

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
	 * @Assert\LessThanOrEqual(
	 *     value="now",
	 *     message="La date d'occurence doit être inférieure ou égale de la date du jour"
	 * )
     */
    private $occurenceAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
	 *
	 * @var int|null
     */
    private $closedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
	 *
	 * @var int|null
     */
    private $declaredAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="deviations")
	 *
	 * @var User|null
     */
    private $declaredBy;

    /**
     * @ORM\Column(type="boolean", nullable=true)
	 *
	 * @var bool|null
     */
    private $isCrexSubmission;

    /**
     * @ORM\Column(type="integer", nullable=true)
	 *
	 * @var int|null
     */
    private $potentialImpact;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $potentialImpactDescription;

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
     * @ORM\OneToMany (targetEntity=DeviationCorrection::class, mappedBy="deviation")
     *
     * @var ArrayCollection<int, DeviationCorrection>
     */
    private $deviationCorrections;

    /**
     * @ORM\OneToMany(targetEntity=DeviationAction::class, mappedBy="deviation")
     *
     * @var ArrayCollection<int, DeviationAction>
     */
    private $actions;

    /**
     * @ORM\OneToMany(targetEntity=DeviationReview::class, mappedBy="deviation")
     *
     * @var ArrayCollection<int, DeviationReview>
     */
    private $reviews;

	/**
	 * @ORM\OneToMany(targetEntity=DeviationAndSample::class, mappedBy="deviation")
	 *
	 * @var ArrayCollection<int, DeviationAndSample>
	 */
	private $deviationAndSamples;

    /**
     * Deviation constructor.
     */
    public function __construct()
    {
        $this->actions = new ArrayCollection();
        $this->reviews = new ArrayCollection();
		$this->deviationAndSamples = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getCode();
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
     * @return DeviationType|null
     */
    public function getType(): ?DeviationType
    {
        return $this->type;
    }

    /**
     * @param DeviationType|null $type
     */
    public function setType(?DeviationType $type): void
    {
        $this->type = $type;
    }

    /**
     * @return DeviationType|null
     */
    public function getSubType(): ?DeviationType
    {
        return $this->subType;
    }

    /**
     * @param DeviationType|null $subType
     */
    public function setSubType(?DeviationType $subType): void
    {
        $this->subType = $subType;
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
     * @param mixed $description
     */
    public function setDescription($description): void
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
     * @return int|null
     */
    public function getGrade(): ?int
    {
        return $this->grade;
    }

    /**
     * @param mixed $grade
     *
     * @throws \Exception
     */
    public function setGrade($grade): void
    {
        if (!array_key_exists($grade, self::GRADES) && $grade !== null) {
            throw new \Exception('the grade '.$grade.' is not authorize !');
        }

        $this->grade = $grade;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
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
    public function getOccurenceAt()
    {
        return $this->occurenceAt;
    }

    /**
     * @param mixed $occurenceAt
     */
    public function setOccurenceAt($occurenceAt): void
    {
        $this->occurenceAt = $occurenceAt;
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
    public function setClosedAt($closedAt): Deviation
    {
        $this->closedAt = $closedAt;

        return $this;
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

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    /**
     * @return Center
     */
    public function getCenter(): ?Center
    {
        return $this->center;
    }

    /**
     * @param Center|null $center
     */
    public function setCenter(?Center $center): void
    {
        $this->center = $center;
    }

    public function getInstitution(): ?Institution
    {
        return $this->institution;
    }

    public function setInstitution(?Institution $institution): void
    {
        $this->institution = $institution;
    }

    /**
     * @return Patient
     */
    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    /**
     * @param Patient|null $patient
     */
    public function setPatient(?Patient $patient): void
    {
        $this->patient = $patient;
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
    public function getPotentialImpact()
    {
        return $this->potentialImpact;
    }

    /**
     * @param mixed $potentialImpact
     *
     * @throws \Exception
     */
    public function setPotentialImpact($potentialImpact): void
    {
        if (!array_key_exists($potentialImpact, self::POTENTIAL_IMPACT) && $potentialImpact !== null) {
            throw new \Exception('this potential impact '.$potentialImpact.' not exist !');
        }

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
		if (!array_key_exists($efficiencyMeasure, self::EFFICIENCY_MEASURE) && $efficiencyMeasure) {
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
     * @return Collection|DeviationAction[]
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }

    /**
     * @return $this
     */
    public function addAction(DeviationAction $action): self
    {
        if (!$this->actions->contains($action)) {
            $this->actions[] = $action;
            $action->setDeviation($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeAction(DeviationAction $action): self
    {
        if ($this->actions->removeElement($action)) {
            // set the owning side to null (unless already changed)
            if ($action->getDeviation() === $this) {
                $action->setDeviation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DeviationReview[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    /**
     * @return $this
     */
    public function addReview(DeviationReview $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setDeviation($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeReview(DeviationReview $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getDeviation() === $this) {
                $review->setDeviation(null);
            }
        }

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
        $actions = $this->actions;

        if ($actions) {
            foreach ($actions as $action) {
                if (!$action->getDoneAt() && null === $action->getDeletedAt()) {
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
