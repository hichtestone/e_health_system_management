<?php

namespace App\ESM\Entity;

use App\ESM\Repository\PhaseSettingRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=PhaseSettingRepository::class)
 * @UniqueEntity(
 *     fields={"label", "project"}, message="Entity.name_unique_error"
 *  )
 */
class PhaseSetting implements AuditrailableInterface
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
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $label;

	/**
	 * @ORM\ManyToOne(targetEntity=PhaseSettingStatus::class, inversedBy="phaseSettings")
	 * @var PhaseSettingStatus
	 */
	private $phaseSettingStatus;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $deletedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Project
     */
    private $project;

    /**
     * @ORM\OneToMany(targetEntity=Visit::class, mappedBy="phase")
     *
     * @var ArrayCollection<int, Visit>
     */
    private $visits;

    /**
     * @ORM\OneToMany(targetEntity=SchemaCondition::class, mappedBy="phase")
     */
    private $schemaConditions;

    public function __construct()
    {
        $this->visits = new ArrayCollection();
        $this->schemaConditions = new ArrayCollection();
    }

	/**
	 * @return string
	 */
    public function __toString()
    {
        return $this->label;
    }

	/**
	 * @return int|null
	 */
    public function getId(): ?int
    {
        return $this->id;
    }

	/**
	 * @return array
	 */
    public function getFieldsToBeIgnored(): array
    {
        return [];
    }

	/**
	 * @return int|null
	 */
    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

	/**
	 * @param int $ordre
	 */
    public function setOrdre(int $ordre): void
    {
        $this->ordre = $ordre;
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
	 * @return string|null
	 */
    public function getLabel(): ?string
    {
        return $this->label;
    }

	/**
	 * @param string|null $label
	 */
    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

	/**
	 * @return PhaseSettingStatus
	 */
	public function getPhaseSettingStatus(): ?PhaseSettingStatus
	{
		return $this->phaseSettingStatus;
	}

	/**
	 * @param PhaseSettingStatus $phaseSettingStatus
	 */
	public function setPhaseSettingStatus(PhaseSettingStatus $phaseSettingStatus): void
	{
		$this->phaseSettingStatus = $phaseSettingStatus;
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
     * @return ArrayCollection<int, Visit>
     */
    public function getVisits(): Collection
    {
        return $this->visits;
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
            $schemaCondition->setPhase($this);
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
            // set the owning side to null (unless already changed)
            if ($schemaCondition->getPhase() === $this) {
                $schemaCondition->setPhase(null);
            }
        }

        return $this;
    }
}
