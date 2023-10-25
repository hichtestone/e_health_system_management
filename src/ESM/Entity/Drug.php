<?php

namespace App\ESM\Entity;

use App\ESM\Entity\DropdownList\TrailTreatment;
use App\ESM\Repository\DrugRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DrugRepository::class)
 */
class Drug implements AuditrailableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isValid = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=TrailTreatment::class, inversedBy="drugs")
     */
    private $trailTreatment;

    /**
     * @ORM\OneToMany(targetEntity=DocumentTransverse::class, mappedBy="drug")
     */
    private $documentTransverses;

	/**
	 * @ORM\OneToMany(targetEntity=ProjectTrailTreatment::class, mappedBy="drug")
	 */
	private $projectTrailTreatments;

    /**
     * @ORM\ManyToMany(targetEntity=Project::class, inversedBy="drugs")
     * @ORM\JoinTable(name="project_drug")
     */
    private $projects;

    public function __construct()
    {
        $this->documentTransverses 	= new ArrayCollection();
        $this->projects 			= new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIsValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(bool $isValid): self
    {
        $this->isValid = $isValid;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTrailTreatment(): ?TrailTreatment
    {
        return $this->trailTreatment;
    }

    public function setTrailTreatment(?TrailTreatment $trailTreatment): self
    {
        $this->trailTreatment = $trailTreatment;

        return $this;
    }

    /**
     * @return Collection|DocumentTransverse[]
     */
    public function getDocumentTransverses(): Collection
    {
        return $this->documentTransverses;
    }


    public function getFieldsToBeIgnored(): array
    {
        return ['documentTransverses'];
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

	/**
	 * @param Project $project
	 * @return $this
	 */
    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
        }

        return $this;
    }

	/**
	 * @param Project $project
	 * @return $this
	 */
    public function removeProject(Project $project): self
    {
        $this->projects->removeElement($project);

        return $this;
    }

	/**
	 * @return Collection|ProjectTrailTreatment[]
	 */
	public function getProjectTrailTreatments(): Collection
	{
		return $this->projectTrailTreatments;
	}

	/**
	 * @param ProjectTrailTreatment $projectTrailTreatment
	 * @return $this
	 */
	public function addProjectTrailTreatment(ProjectTrailTreatment $projectTrailTreatment): self
	{
		if (!$this->projectTrailTreatments->contains($projectTrailTreatment)) {
			$this->projectTrailTreatments[] = $projectTrailTreatment;
			$projectTrailTreatment->setDrug($this);
		}

		return $this;
	}

	/**
	 * @param ProjectTrailTreatment $projectTrailTreatment
	 * @return $this
	 */
	public function removeProjectTrailTreatment(ProjectTrailTreatment $projectTrailTreatment): self
	{
		if ($this->documentTransverses->removeElement($projectTrailTreatment)) {
			$projectTrailTreatment->setDrug(null);
		}

		return $this;
	}
}
