<?php

namespace App\ESM\Entity;

use App\ESM\Repository\VisitPatientRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=VisitPatientRepository::class)
 */
class VisitPatient implements AuditrailableInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
	 *
	 * @Groups("visit-patient")
	 *
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="visits")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Patient
	 *
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity=Visit::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Visit
	 *
     */
    private $visit;

    /**
     * @ORM\ManyToOne(targetEntity=PatientVariable::class)
     *
     * @var PatientVariable
     */
    private $variable;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $iteration;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var string
     */
    private $sourceId;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $occuredAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $monitoredAt;

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
     * @ORM\ManyToOne(targetEntity=VisitPatientStatus::class)
     * @ORM\JoinColumn(nullable=true)
     *
     * @var VisitPatientStatus|null
	 *
     */
    private $status;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $badge;

    /**
     * @ORM\OneToMany(targetEntity=ConditionVisitPatient::class, mappedBy="VisitPatient")
     */
    private $conditionVisitPatients;

    public function __construct()
    {
        $this->conditionVisitPatients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function displayVisitPatient(): string
    {
        return $this->patient->getNumber().' '.$this->visit->getLabel();
    }

    public function getFieldsToBeIgnored(): array
    {
        return ['deletedAt', 'disabledAt', 'iteration', 'sourceId'];
    }

    public function getPatient(): Patient
    {
        return $this->patient;
    }

    public function setPatient(Patient $patient): void
    {
        $this->patient = $patient;
    }

    public function getVisit(): ?Visit
    {
        return $this->visit;
    }

    public function setVisit(Visit $visit): void
    {
        $this->visit = $visit;
    }

    public function getVariable(): PatientVariable
    {
        return $this->variable;
    }

    public function setVariable(PatientVariable $variable): void
    {
        $this->variable = $variable;
    }

    public function getIteration(): int
    {
        return $this->iteration;
    }

    public function setIteration(int $iteration): void
    {
        $this->iteration = $iteration;
    }

    public function getSourceId(): ?string
    {
        return $this->sourceId;
    }

    public function setSourceId(string $sourceId): void
    {
        $this->sourceId = $sourceId;
    }

    public function getStatus(): ?VisitPatientStatus
    {
        return $this->status;
    }

    public function setStatus(?VisitPatientStatus $status): void
    {
        $this->status = $status;
    }

    public function getBadge(): ?string
    {
        return $this->badge;
    }

    public function setBadge(?string $badge): void
    {
        $this->badge = $badge;
    }

    public function getOccuredAt(): ?DateTime
    {
        return $this->occuredAt;
    }

    public function setOccuredAt(?DateTime $occuredAt): void
    {
        $this->occuredAt = $occuredAt;
    }

    public function getMonitoredAt(): ?DateTime
    {
        return $this->monitoredAt;
    }

    public function setMonitoredAt(?DateTime $monitoredAt): void
    {
        $this->monitoredAt = $monitoredAt;
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

    public function getDisabledAt(): ?DateTime
    {
        return $this->disabledAt;
    }

    public function setDisabledAt(?DateTime $disabledAt): self
    {
        $this->disabledAt = $disabledAt;

        return $this;
    }

    public function __toString()
    {
       return $this->getPatient()->getNumber() ?? '';
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
            $conditionVisitPatient->setVisitPatient($this);
        }

        return $this;
    }

    public function removeConditionVisitPatient(ConditionVisitPatient $conditionVisitPatient): self
    {
        if ($this->conditionVisitPatients->removeElement($conditionVisitPatient)) {
            // set the owning side to null (unless already changed)
            if ($conditionVisitPatient->getVisitPatient() === $this) {
                $conditionVisitPatient->setVisitPatient(null);
            }
        }

        return $this;
    }
}
