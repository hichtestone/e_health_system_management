<?php

namespace App\ESM\Entity;

use App\ESM\Repository\PatientDataRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=PatientDataRepository::class)
 * @UniqueEntity("patient, variable")
 */
class PatientData implements AuditrailableInterface
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
     * @ORM\ManyToOne(targetEntity=Patient::class)
     *
     * @var Patient
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity=PatientVariable::class)
     *
     * @var PatientVariable
     */
    private $variable;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string|null
     */
    private $variableValue;

    /**
     * @ORM\Column(type="smallint")
     *
     * @var int
     */
    private $iteration = 1;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     *
     * @var bool
     */
    private $importing = false;

    /**
     * @ORM\Column(type="smallint", nullable=false)
     *
     * @var int
     */
    private $ordre;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $deletedAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $disabledAt = null;

    /**
     * @ORM\OneToMany(targetEntity=ConditionPatientData::class, mappedBy="PatientData")
     */
    private $conditionPatientData;

	/**
	 * PatientData constructor.
	 */
    public function __construct()
    {
        $this->conditionPatientData = new ArrayCollection();
    }

	/**
	 * @return int|null
	 */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString()
	{
    	return $this->patient->getNumber();
	}

	/**
	 * @return array
	 */
    public function getFieldsToBeIgnored(): array
    {
        return ['iteration', 'ordre', 'importing'];
    }

	/**
	 * @return Patient
	 */
    public function getPatient(): Patient
    {
        return $this->patient;
    }

	/**
	 * @param Patient $patient
	 */
    public function setPatient(Patient $patient): void
    {
        $this->patient = $patient;
    }

	/**
	 * @return PatientVariable
	 */
    public function getVariable(): PatientVariable
    {
        return $this->variable;
    }

	/**
	 * @param PatientVariable $variable
	 */
    public function setVariable(PatientVariable $variable): void
    {
        $this->variable = $variable;
    }

	/**
	 * @return string|null
	 */
    public function getVariableValue(): ?string
    {
        return $this->variableValue;
    }

	/**
	 * @param string $variableValue
	 */
    public function setVariableValue(string $variableValue): void
    {
        $this->variableValue = $variableValue;
    }

	/**
	 * @return int
	 */
    public function getIteration(): int
    {
        return $this->iteration;
    }

	/**
	 * @param int $iteration
	 */
    public function setIteration(int $iteration): void
    {
        $this->iteration = $iteration;
    }

	/**
	 * @return bool
	 */
    public function isImporting(): bool
    {
        return $this->importing;
    }

	/**
	 * @param bool $importing
	 */
    public function setImporting(bool $importing): void
    {
        $this->importing = $importing;
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
	 * @return $this
	 */
    public function setOrdre(int $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
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
	 * @return DateTime|null
	 */
    public function getDisabledAt(): ?DateTime
    {
        return $this->disabledAt;
    }

	/**
	 * @param DateTime|null $disabledAt
	 * @return $this
	 */
    public function setDisabledAt(?DateTime $disabledAt): self
    {
        $this->disabledAt = $disabledAt;

        return $this;
    }

    /**
     * @return Collection|ConditionPatientData[]
     */
    public function getConditionPatientData(): Collection
    {
        return $this->conditionPatientData;
    }

	/**
	 * @param ConditionPatientData $conditionPatientData
	 * @return $this
	 */
    public function addConditionPatientData(ConditionPatientData $conditionPatientData): self
    {
        if (!$this->conditionPatientData->contains($conditionPatientData)) {
            $this->conditionPatientData[] = $conditionPatientData;
            $conditionPatientData->setPatientData($this);
        }

        return $this;
    }

	/**
	 * @param ConditionPatientData $conditionPatientData
	 * @return $this
	 */
    public function removeConditionPatientData(ConditionPatientData $conditionPatientData): self
    {
        if ($this->conditionPatientData->removeElement($conditionPatientData)) {
            // set the owning side to null (unless already changed)
            if ($conditionPatientData->getPatientData() === $this) {
                $conditionPatientData->setPatientData(null);
            }
        }

        return $this;
    }
}
