<?php

namespace App\ESM\Entity;

use App\ESM\Repository\SchemaConditionRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SchemaConditionRepository::class)
 */
class SchemaCondition implements AuditrailableInterface
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
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $label;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Project
     */
    private $project;

    /**
     * @ORM\Column(type="text", nullable=true, name="`condition`")
     */
    private $condition;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $deletedAt;

    /**
     * @ORM\ManyToOne(targetEntity=PhaseSetting::class, inversedBy="schemaConditions")
     */
    private $phase;

    /**
     * @ORM\ManyToOne(targetEntity=Visit::class, inversedBy="schemaConditions")
     */
    private $visit;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $disabled;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $phaseVisit;

    /**
     * @ORM\ManyToMany(targetEntity=PatientVariable::class, inversedBy="schemaConditions")
     */
    private $patientVariable;

    /**
     * @ORM\OneToMany(targetEntity=ConditionPatientData::class, mappedBy="SchemaCondition")
     */
    private $conditionPatientData;

    /**
     * @ORM\OneToMany(targetEntity=ConditionVisitPatient::class, mappedBy="SchemaCondition")
     */
    private $conditionVisitPatients;

    public function __construct()
    {
        $this->patientVariable = new ArrayCollection();
        $this->conditionPatientData = new ArrayCollection();
        $this->conditionVisitPatients = new ArrayCollection();
    }

    public function getFieldsToBeIgnored(): array
    {
        return [];
    }

    public function __toString(): string
    {
        return $this->label ?? '';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    public function getCondition(): ?string
    {
        return $this->condition;
    }

    public function setCondition(?string $condition): void
    {
        $this->condition = $condition;
    }

    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTime $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    public function getPhase(): ?PhaseSetting
    {
        return $this->phase;
    }

    public function setPhase(?PhaseSetting $phase): self
    {
        $this->phase = $phase;

        return $this;
    }

    public function getVisit(): ?Visit
    {
        return $this->visit;
    }

    public function setVisit(?Visit $visit): self
    {
        $this->visit = $visit;

        return $this;
    }

    public function getDisabled(): ?bool
    {
        return $this->disabled;
    }

    public function setDisabled(?bool $disabled): self
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function getPhaseVisit(): ?string
    {
        return $this->phaseVisit;
    }

    public function setPhaseVisit(string $phaseVisit): self
    {
        $this->phaseVisit = $phaseVisit;

        return $this;
    }

    /**
     * @return Collection|PatientVariable[]
     */
    public function getPatientVariable(): Collection
    {
        return $this->patientVariable;
    }

    public function addPatientVariable(PatientVariable $patientVariable): self
    {
        if (!$this->patientVariable->contains($patientVariable)) {
            $this->patientVariable[] = $patientVariable;
        }

        return $this;
    }

    public function removePatientVariable(PatientVariable $patientVariable): self
    {
        $this->patientVariable->removeElement($patientVariable);

        return $this;
    }

    /**
     * @return Collection|ConditionPatientData[]
     */
    public function getConditionPatientData(): Collection
    {
        return $this->conditionPatientData;
    }

    public function addConditionPatientData(ConditionPatientData $conditionPatientData): self
    {
        if (!$this->conditionPatientData->contains($conditionPatientData)) {
            $this->conditionPatientData[] = $conditionPatientData;
            $conditionPatientData->setSchemaCondition($this);
        }

        return $this;
    }

    public function removeConditionPatientData(ConditionPatientData $conditionPatientData): self
    {
        if ($this->conditionPatientData->removeElement($conditionPatientData)) {
            // set the owning side to null (unless already changed)
            if ($conditionPatientData->getSchemaCondition() === $this) {
                $conditionPatientData->setSchemaCondition(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ConditionVisitPatient[]
     */
    public function getConditionVisitPatients(): Collection
    {
        return $this->conditionVisitPatients;
    }

    public function addConditionVisitPatient(ConditionVisitPatient $conditionVisitPatient): self
    {
        if (!$this->conditionVisitPatients->contains($conditionVisitPatient)) {
            $this->conditionVisitPatients[] = $conditionVisitPatient;
            $conditionVisitPatient->setSchemaCondition($this);
        }

        return $this;
    }

    public function removeConditionVisitPatient(ConditionVisitPatient $conditionVisitPatient): self
    {
        if ($this->conditionVisitPatients->removeElement($conditionVisitPatient)) {
            // set the owning side to null (unless already changed)
            if ($conditionVisitPatient->getSchemaCondition() === $this) {
                $conditionVisitPatient->setSchemaCondition(null);
            }
        }

        return $this;
    }
}
