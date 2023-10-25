<?php

namespace App\ESM\Entity;

use App\ESM\Repository\PatientVariableRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=PatientVariableRepository::class)
 * @UniqueEntity(
 *     fields={"label", "project"}, message="Entity.name_unique_error"
 *  )
 */
class PatientVariable implements AuditrailableInterface
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
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $label;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     *
     * @var int|null
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class)
     *
     * @var Project
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity=VariableType::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var VariableType
     */
    private $variableType;

    /**
     * @ORM\Column(type="string", length=55, nullable=true)
     *
     * @var string
     */
    private $sourceId;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @var bool|null
     */
    private $hasPatient;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @var bool|null
     */
    private $hasVisit;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @var bool|null
     */
    private $isVisit;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @var bool|null
     */
    private $isExam;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @var bool|null
     */
    private $isVariable;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     *
     * @var bool
     */
    private $sys = false;

    /**
     * @ORM\ManyToMany(targetEntity=Visit::class, mappedBy="variables")
     * @ORM\JoinTable(name="visit_variable")
     *
     * @var ArrayCollection<int, Visit>
     */
    private $visits;

	/**
	 * @ORM\ManyToMany(targetEntity=Exam::class, mappedBy="variables")
	 * @ORM\JoinTable(name="exam_variable")
	 *
	 * @var ArrayCollection<int, Exam>
	 */
	private $exams;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $deletedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Exam::class, inversedBy="patientVariables")
     * @ORM\JoinColumn(nullable=true)
     */
    private $exam;

	/**
	 * @ORM\ManyToOne(targetEntity=Visit::class, inversedBy="patientVariablesVisit")
	 * @ORM\JoinColumn(nullable=true)
     *
     * @var Visit|null
	 */
	private $visit;

    /**
     * @ORM\ManyToOne(targetEntity=VariableList::class, inversedBy="patientVariables")
     * @ORM\JoinColumn(nullable=true)
     */
    private $variableList;

    /**
     * @ORM\ManyToMany(targetEntity=SchemaCondition::class, mappedBy="patientVariable")
     */
    private $schemaConditions;

	/**
	 *
	 */
    public function __construct()
    {
        $this->visits 			= new ArrayCollection();
		$this->exams 			= new ArrayCollection();
        $this->schemaConditions = new ArrayCollection();
    }

	/**
	 * @return int|null
	 */
    public function getId(): ?int
    {
        return $this->id;
    }

	/**
	 * @return string[]
	 */
    public function getFieldsToBeIgnored(): array
    {
        return ['project', 'source_id', 'hasPatient', 'isVisit', 'isExam', 'isVariable', 'list', 'visits'];
    }

	/**
	 * @return string
	 */
    public function __toString(): string
    {
        return $this->getLabel();
    }

	/**
	 * @return string
	 */
    public function getLabel(): string
    {
        return $this->label;
    }

	/**
	 * @param string $label
	 */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

	/**
	 * @return int|null
	 */
    public function getPosition(): ?int
    {
        return $this->position;
    }

	/**
	 * @param int|null $position
	 */
    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

	/**
	 * @return Project
	 */
    public function getProject(): Project
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
	 * @return VariableType|null
	 */
    public function getVariableType(): ?VariableType
    {
        return $this->variableType;
    }

	/**
	 * @param VariableType $variableType
	 */
    public function setVariableType(VariableType $variableType): void
    {
        $this->variableType = $variableType;
    }

	/**
	 * @return string|null
	 */
    public function getSourceId(): ?string
    {
        return $this->sourceId;
    }

	/**
	 * @param string $sourceId
	 */
    public function setSourceId(string $sourceId): void
    {
        $this->sourceId = $sourceId;
    }

	/**
	 * @return bool|null
	 */
    public function isHasPatient(): ?bool
    {
        return $this->hasPatient;
    }

	/**
	 * @param bool|null $hasPatient
	 */
    public function setHasPatient(?bool $hasPatient): void
    {
        $this->hasPatient = $hasPatient;
    }

	/**
	 * @return bool|null
	 */
    public function isHasVisit(): ?bool
    {
        return $this->hasVisit;
    }

	/**
	 * @param bool|null $hasVisit
	 */
    public function setHasVisit(?bool $hasVisit): void
    {
        $this->hasVisit = $hasVisit;
    }

	/**
	 * @return bool|null
	 */
    public function isVisit(): ?bool
    {
        return $this->isVisit;
    }

	/**
	 * @param bool|null $isVisit
	 */
    public function setIsVisit(?bool $isVisit): void
    {
        $this->isVisit = $isVisit;
    }

	/**
	 * @return bool|null
	 */
    public function isExam(): ?bool
    {
        return $this->isExam;
    }

	/**
	 * @param bool|null $isExam
	 */
    public function setIsExam(?bool $isExam): void
    {
        $this->isExam = $isExam;
    }

	/**
	 * @return bool|null
	 */
    public function isVariable(): ?bool
    {
        return $this->isVariable;
    }

	/**
	 * @param bool|null $isVariable
	 */
    public function setIsVariable(?bool $isVariable): void
    {
        $this->isVariable = $isVariable;
    }

	/**
	 * @return bool
	 */
    public function isSys(): bool
    {
        return $this->sys;
    }

	/**
	 * @param bool $isSys
	 */
    public function setSys(bool $isSys): void
    {
        $this->sys = $isSys;
    }

    /**
     * @return ArrayCollection<int, Visit>
     */
    public function getVisits(): Collection
    {
        return $this->visits;
    }

	/**
	 * @return ArrayCollection<int, Exam>
	 */
	public function getExams(): Collection
	{
		return $this->exams;
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

	/**
	 * @return Exam|null
	 */
    public function getExam(): ?Exam
    {
        return $this->exam;
    }

	/**
	 * @param Exam|null $exam
	 * @return $this
	 */
    public function setExam(?Exam $exam): self
    {
        $this->exam = $exam;

        return $this;
    }

    /**
     * @return Visit|null
     */
    public function getVisit(): ?Visit
    {
        return $this->visit;
    }

    /**
     * @param Visit|null $visit
     */
    public function setVisit(?Visit $visit): void
    {
        $this->visit = $visit;
    }

	/**
	 * @return VariableList|null
	 */
    public function getVariableList(): ?VariableList
    {
        return $this->variableList;
    }

	/**
	 * @param VariableList|null $variableList
	 * @return $this
	 */
    public function setVariableList(?VariableList $variableList): self
    {
        $this->variableList = $variableList;

        return $this;
    }

    /**
     * @return Collection|SchemaCondition[]
     */
    public function getSchemaConditions(): Collection
    {
        return $this->schemaConditions;
    }

	/**
	 * @param SchemaCondition $schemaCondition
	 * @return $this
	 */
    public function addSchemaCondition(SchemaCondition $schemaCondition): self
    {
        if (!$this->schemaConditions->contains($schemaCondition)) {
            $this->schemaConditions[] = $schemaCondition;
            $schemaCondition->addPatientVariable($this);
        }

        return $this;
    }

	/**
	 * @param SchemaCondition $schemaCondition
	 * @return $this
	 */
    public function removeSchemaCondition(SchemaCondition $schemaCondition): self
    {
        if ($this->schemaConditions->removeElement($schemaCondition)) {
            $schemaCondition->removePatientVariable($this);
        }

        return $this;
    }
}
