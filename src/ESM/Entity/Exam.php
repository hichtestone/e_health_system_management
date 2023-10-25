<?php

namespace App\ESM\Entity;

use App\ESM\Entity\DropdownList\ExamType;
use App\ESM\Repository\ExamRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ExamRepository::class)
 * @UniqueEntity(
 *     fields={"name", "project"},message="Entity.name_unique_error"
 *  )
 */
class Exam implements AuditrailableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     *
     * @var int
     */
    private $position;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     */
    private $sourceId;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price = 0;

    /**
     * @ORM\ManyToOne(targetEntity=ExamType::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var ExamType
     */
    private $type;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 * @var string|null
	 */
	private $typeReason;

    /**
     * @ORM\Column(type="smallint", nullable=false)
     *
     * @var int
     */
    private $ordre;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Project
     */
    private $project;

	/**
	 * @ORM\ManyToMany(targetEntity=PatientVariable::class, inversedBy="exams")
	 * @ORM\JoinTable(name="exam_variable")
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
     * @ORM\OneToMany(targetEntity=PatientVariable::class, mappedBy="exam")
     */
    private $patientVariables;

    public function __construct()
    {
    	$this->variables = new ArrayCollection();
        $this->patientVariables = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFieldsToBeIgnored(): array
    {
        return ['deletedAt'];
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getSourceId(): ?int
    {
        return $this->sourceId;
    }

    public function setSourceId(int $sourceId): void
    {
        $this->sourceId = $sourceId;
    }

    public function getType(): ?ExamType
    {
        return $this->type;
    }

    public function setType(ExamType $type): void
    {
        $this->type = $type;
    }

	/**
	 * @return string|null
	 */
	public function getTypeReason(): ?string
	{
		return $this->typeReason;
	}

	/**
	 * @param string|null $typeReason
	 */
	public function setTypeReason(?string $typeReason): void
	{
		$this->typeReason = $typeReason;
	}

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
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
     * @return Collection|PatientVariable[]
     */
    public function getPatientVariables(): Collection
    {
        return $this->patientVariables;
    }

    public function addPatientVariable(PatientVariable $patientVariable): self
    {
        if (!$this->patientVariables->contains($patientVariable)) {
            $this->patientVariables[] = $patientVariable;
            $patientVariable->setExam($this);
        }

        return $this;
    }

    public function removePatientVariable(PatientVariable $patientVariable): self
    {
        if ($this->patientVariables->removeElement($patientVariable)) {
            // set the owning side to null (unless already changed)
            if ($patientVariable->getExam() === $this) {
                $patientVariable->setExam(null);
            }
        }

        return $this;
    }
}
