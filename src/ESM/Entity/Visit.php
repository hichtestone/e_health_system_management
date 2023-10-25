<?php

namespace App\ESM\Entity;

use App\ESM\Repository\VisitRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=VisitRepository::class)
 * @UniqueEntity(
 *     fields={"short", "project"}, message="Entity.name_unique_error"
 *  )
 */
class Visit implements AuditrailableInterface
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
     * @ORM\Column(type="string", length=255, nullable=false)
     *
     * @var string
     */
    private $short;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string|null
     */
    private $label;

    /**
     * @ORM\ManyToOne(targetEntity=PhaseSetting::class, inversedBy="visits")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var PhaseSetting
     */
    private $phase;

    /**
     * @ORM\Column(type="smallint")
     *
     * @var int
     */
    private $ordre;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $position = 1;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     *
     * @var int
     */
    private $delay;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     *
     * @var int
     */
    private $delayApprox;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     */
    private $sourceId;

    /**
     * @ORM\Column(type="smallint")
     *
     * @var int
     */
    private $price = 0;

    /**
     * @ORM\ManyToOne(targetEntity=PatientVariable::class)
     *
     * @var PatientVariable
     */
    private $patientVariable;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Project
     */
    private $project;

    /**
     * @ORM\ManyToMany(targetEntity=PatientVariable::class, inversedBy="visits")
     * @ORM\JoinTable(name="visit_variable")
     *
     * @var ArrayCollection<int, PatientVariable>
     */
    private $variables;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $deletedAt;

    /**
     * @ORM\OneToMany(targetEntity=SchemaCondition::class, mappedBy="visit")
     */
    private $schemaConditions;

	/**
	 * @ORM\OneToMany(targetEntity=PatientVariable::class, mappedBy="visit")
	 * @ORM\JoinColumn(nullable=false)
	 * @var ArrayCollection<int, PatientVariable>
	 */
	private $patientVariablesVisit;

    public function __construct()
    {
        $this->variables 			 = new ArrayCollection();
        $this->schemaConditions 	 = new ArrayCollection();
		$this->patientVariablesVisit = new ArrayCollection();
	}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFieldsToBeIgnored(): array
    {
        return ['project'];
    }

    public function __toString(): string
    {
        return $this->getShort();
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getShort(): ?string
    {
        return $this->short;
    }

    public function setShort(string $short): void
    {
        $this->short = $short;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): void
    {
        $this->ordre = $ordre;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getSourceId(): ?int
    {
        return $this->sourceId;
    }

    public function setSourceId(int $sourceId): void
    {
        $this->sourceId = $sourceId;
    }

    public function getPhase(): ?PhaseSetting
    {
        return $this->phase;
    }

    public function setPhase(PhaseSetting $phase): void
    {
        $this->phase = $phase;
    }

    public function getDelay(): ?int
    {
        return $this->delay;
    }

    public function setDelay(?int $delay): void
    {
        $this->delay = $delay;
    }

    public function getDelayApprox(): ?int
    {
        return $this->delayApprox;
    }

    public function setDelayApprox(?int $delayApprox): void
    {
        $this->delayApprox = $delayApprox;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPatientVariable(): ?PatientVariable
    {
        return $this->patientVariable;
    }

    public function setPatientVariable(?PatientVariable $patientVariable): void
    {
        $this->patientVariable = $patientVariable;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    /**
     * @return ArrayCollection<int, PatientVariable>
     */
    public function getVariables(): Collection
    {
        return $this->variables;
    }

    public function addVariable(PatientVariable $patientVariable): self
    {
        if (!$this->variables->contains($patientVariable)) {
            $this->variables[] = $patientVariable;
        }

        return $this;
    }

    public function removeVariable(PatientVariable $patientVariable): self
    {
        if ($this->variables->contains($patientVariable)) {
            $this->variables->removeElement($patientVariable);
        }

        return $this;
    }

    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return Collection|SchemaCondition[]
     */
    public function getSchemaConditions(): Collection
    {
        return $this->schemaConditions;
    }

    public function addSchemaCondition(SchemaCondition $schemaCondition): self
    {
        if (!$this->schemaConditions->contains($schemaCondition)) {
            $this->schemaConditions[] = $schemaCondition;
            $schemaCondition->setVisit($this);
        }

        return $this;
    }

    public function removeSchemaCondition(SchemaCondition $schemaCondition): self
    {
        if ($this->schemaConditions->removeElement($schemaCondition)) {
            // set the owning side to null (unless already changed)
            if ($schemaCondition->getVisit() === $this) {
                $schemaCondition->setVisit(null);
            }
        }

        return $this;
    }

	/**
	 * @return ArrayCollection<int, PatientVariable>
	 */
	public function getPatientVariablesVisit(): Collection
	{
		return $this->patientVariablesVisit;
	}

	/**
	 * @param PatientVariable $patientVariable
	 * @return Visit
	 */
	public function addPatientVariableVisit(PatientVariable $patientVariable): self
	{
		if (!$this->patientVariablesVisit->contains($patientVariable)) {
			$this->patientVariablesVisit[] = $patientVariable;
			$patientVariable->getVisit($this);
		}

		return $this;
	}

	/**
	 * @param PatientVariable $patientVariable
	 * @return $this
	 */
	public function removePatientVariableVisit(PatientVariable $patientVariable): self
	{
		if ($this->patientVariablesVisit->removeElement($patientVariable)) {
			// set the owning side to null (unless already changed)
			if ($patientVariable->getVisit() === $this) {
				$patientVariable->setVisit(null);
			}
		}
	}
}
