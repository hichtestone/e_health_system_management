<?php

namespace App\ESM\Entity;

use App\ESM\Repository\PatientRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PatientRepository::class)
 */
class Patient implements AuditrailableInterface
{
    /**
	 *
	 * @Groups("center-patient")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
	 *
	 * @Groups("center-patient")
     * @ORM\Column(type="string", length=20)
     *
     * @var string
     */
    private $number;

    /**
     * @ORM\ManyToOne(targetEntity=Center::class, inversedBy="patients")
     *
     * @var Center
     */
    private $center;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $consentAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $inclusionAt;

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
     * @ORM\OneToMany (targetEntity=Deviation::class, mappedBy="patient")
     *
     * @var ArrayCollection<int, Deviation>
     */
    private $deviations;

    /**
     * @ORM\oneToMany(targetEntity=AdverseEvent::class, mappedBy="patient")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var AdverseEvent
     */
    private $adverseEvents;

	/**
	 * @ORM\OneToMany (targetEntity=VisitPatient::class, mappedBy="patient")
	 *
	 * @var ArrayCollection<int, VisitPatient>
	 */
	private $visits;

    public function __construct()
    {
        $this->deviations 	 = new ArrayCollection();
        $this->adverseEvents = new ArrayCollection();
        $this->visits 		 = new ArrayCollection();
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
        return $this->getNumber();
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    public function getCenter(): ?Center
    {
        return $this->center;
    }

    public function setCenter(Center $center): void
    {
        $this->center = $center;
    }

    public function getConsentAt(): ?DateTime
    {
        return $this->consentAt;
    }

    public function setConsentAt(?DateTime $consentAt): void
    {
        $this->consentAt = $consentAt;
    }

    public function getInclusionAt(): ?DateTime
    {
        return $this->inclusionAt;
    }

    public function setInclusionAt(?DateTime $inclusionAt): void
    {
        $this->inclusionAt = $inclusionAt;
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

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    /**
     * @return Collection|Deviation[]
     */
    public function getDeviations(): Collection
    {
        return $this->deviations;
    }

    /**
     * @return $this
     */
    public function addDeviation(Deviation $deviation): self
    {
        if (!$this->deviations->contains($deviation)) {
            $this->deviations[] = $deviation;
            $deviation->setPatient($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeDeviation(Deviation $deviation): self
    {
        if ($this->deviations->removeElement($deviation)) {
            // set the owning side to null (unless already changed)
            if ($deviation->getPatient() === $this) {
                $deviation->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AdverseEvent[]
     */
    public function getAdverseEvents(): Collection
    {
        return $this->adverseEvents;
    }

    /**
     * @return $this
     */
    public function addAdverseEvent(AdverseEvent $adverseEvent): self
    {
        if (!$this->adverseEvents->contains($adverseEvent)) {
            $this->adverseEvents[] = $adverseEvent;
            $adverseEvent->setPatient($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeAdverseEvent(AdverseEvent $adverseEvent): self
    {
        if ($this->adverseEvents->removeElement($adverseEvent)) {
            // set the owning side to null (unless already changed)
            if ($adverseEvent->getPatient() === $this) {
                $adverseEvent->setPatient(null);
            }
        }

        return $this;
    }

	/**
	 * @return Collection|VisitPatient[]
	 */
	public function getVisits(): Collection
	{
		return $this->visits;
	}

	/**
	 * @return $this
	 */
	public function addVisit(VisitPatient $visit): self
	{
		if (!$this->visits->contains($visit)) {
			$this->visits[] = $visit;
			$visit->setPatient($this);
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	public function removeVisit(VisitPatient $visit): self
	{
		if ($this->visits->removeElement($visit)) {
			// set the owning side to null (unless already changed)
			if ($visit->getPatient() === $this) {
				$visit->setPatient(null);
			}
		}

		return $this;
	}
}
