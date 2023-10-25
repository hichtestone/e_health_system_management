<?php

namespace App\ESM\Entity;

use App\ESM\Entity\DropdownList\CenterStatus;
use App\ESM\Repository\CenterRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use App\ETMF\Entity\Document;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CenterRepository::class)
 * @UniqueEntity(
 *     fields={"number", "project"},
 *     message="Ce Numéro de centre {{ value }} est déjà utilisé."
 *  )
 */
class Center implements AuditrailableInterface
{
    /**
	 * @Groups("center-patient")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"center"})
     */
    private $id;

    /**
	 * @Groups("center-patient")
     * @ORM\Column(type="string", length=55)
     * @Groups({"center"})
     */
    private $number;

    /**
	 * @Groups("center-patient")
     * @ORM\Column(type="string", length=255)
     * @Groups({"center"})
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Institution::class, inversedBy="centers")
     * @ORM\JoinTable(name="institution_center")
     * @Assert\NotBlank
     *
     * @var Collection<int, Institution>
     */
    private $institutions;

    /**
	 * @Groups("center-patient")
     * @ORM\OneToMany (targetEntity=Patient::class, mappedBy="center")
     *
     * @var ArrayCollection<int, Patient>
     */
    private $patients;

    /**
     * @ORM\OneToMany(targetEntity=InterlocutorCenter::class, mappedBy="center")
     *
     * @var ArrayCollection<int, InterlocutorCenter>
     */
    private $interlocutorCenters;

    /**
     * @ORM\ManyToOne(targetEntity=CenterStatus::class)
     * @ORM\JoinColumn(nullable=true)
     *
     * @Assert\NotBlank
     */
    private $centerStatus;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="centers")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Project
     */
    private $project;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $deletedAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     */
    private $sourceId;

    /**
     * @ORM\OneToMany(targetEntity=Deviation::class, mappedBy="center")
     *
     * @var ArrayCollection<int, Deviation>
     */
    private $deviations;

    /**
     * @ORM\OneToMany(targetEntity=ReportVisit::class, mappedBy="center")
     *
     * @var ArrayCollection<int, ReportVisit>
     */
    private $reportVisits;

    /**
     * @ORM\ManyToMany(targetEntity=Document::class, mappedBy="centers")
     * @ORM\JoinTable(name="document_center")
     *
     * @var ArrayCollection<int, Document>
     */
    private $documents;

    /**
     * Center constructor.
     */
    public function __construct()
    {
        $this->institutions 		= new ArrayCollection();
        $this->patients 			= new ArrayCollection();
        $this->interlocutorCenters 	= new ArrayCollection();
        $this->deviations 			= new ArrayCollection();
        $this->reportVisits 		= new ArrayCollection();
        $this->documents            = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string[]
     */
    public function getFieldsToBeIgnored(): array
    {
        return ['project'];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?? '';
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * @return $this
     */
    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Institution>
     */
    public function getInstitutions(): Collection
    {
        return $this->institutions;
    }

    /**
     * @return $this
     */
    public function addInstitution(Institution $institution): self
    {
        if (!$this->institutions->contains($institution)) {
            $this->institutions[] = $institution;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeInstitution(Institution $institution): self
    {
        if ($this->institutions->contains($institution)) {
            $this->institutions->removeElement($institution);
        }

        return $this;
    }

    /**
     * @return ArrayCollection<int, InterlocutorCenter>
     */
    public function getPrincipalInvestigators(): Collection
    {
        $mem = [];
        $coll = new ArrayCollection();
        foreach ($this->interlocutorCenters as $interlocutorCenter) {
            if ($interlocutorCenter->isPrincipalInvestigator() && null === $interlocutorCenter->getDisabledAt()) {
                if (!in_array($interlocutorCenter->getInterlocutor()->getId(), $mem)) {
                    $coll->add($interlocutorCenter->getInterlocutor());
                    $mem[] = $interlocutorCenter->getInterlocutor()->getId();
                }
            }
        }

        return $coll;
    }

    /**
     * @return ArrayCollection<int, InterlocutorCenter>
     */
    public function getInterlocutorCenters(): Collection
    {
        return $this->interlocutorCenters;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    /**
     * @return $this
     */
    public function setDeletedAt(?DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getCenterStatus(): ?CenterStatus
    {
        return $this->centerStatus;
    }

    public function setCenterStatus(?CenterStatus $centerStatus): void
    {
        $this->centerStatus = $centerStatus;
    }

    public function getSourceId(): int
    {
        return $this->sourceId;
    }

    public function setSourceId(int $sourceId): void
    {
        $this->sourceId = $sourceId;
    }

    public function displayNameNumber(): string
    {
        return $this->getNumber().' - '.$this->getName();
    }

    /**
     * @return ArrayCollection|Deviation[]
     */
    public function getDeviations(): ArrayCollection
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
            $deviation->setCenter($this);
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
            if ($deviation->getCenter() === $this) {
                $deviation->setCenter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Patient[]
     */
    public function getPatients(): Collection
    {
        return $this->patients;
    }

    /**
     * @return $this
     */
    public function addPatient(Patient $patient): self
    {
        if (!$this->patients->contains($patient)) {
            $this->patients[] = $patient;
            $patient->setCenter($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removePatient(Patient $patient): self
    {
        if ($this->patients->removeElement($patient)) {
            // set the owning side to null (unless already changed)
            if ($patient->getCenter() === $this) {
                $patient->setCenter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReportVisit[]
     */
    public function getReportVisits(): Collection
    {
        return $this->reportVisits;
    }

    /**
     * @return $this
     */
    public function addReportVisit(ReportVisit $reportVisit): self
    {
        if (!$this->reportVisits->contains($reportVisit)) {
            $this->reportVisits[] = $reportVisit;
            $reportVisit->setProject($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeReportVisit(ReportVisit $reportVisit): self
    {
        if ($this->reportVisits->removeElement($reportVisit)) {
            // set the owning side to null (unless already changed)
            if ($reportVisit->getProject() === $this) {
                $reportVisit->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    /**
     * @param Document $document
     * @return $this
     */
    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
        }

        return $this;
    }

    /**
     * @param Document $document
     * @return $this
     */
    public function removeDocument(Document $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
        }

        return $this;
    }

}
