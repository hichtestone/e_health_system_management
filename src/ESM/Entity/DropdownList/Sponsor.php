<?php

namespace App\ESM\Entity\DropdownList;

use App\ESM\Entity\Project;
use App\ETMF\Entity\Document;
use App\ESM\Repository\SponsorRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="dl_sponsor")
 * @ORM\Entity(repositoryClass=SponsorRepository::class)
 */
class Sponsor
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     * @Groups({"sponsor"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=55)
     *
     * @var string
     * @Groups({"sponsor"})
     */
    private $label;

	/**
	 * @ORM\Column(type="string", length=255)
	 *
	 * @var string
     * @Groups({"sponsor"})
	 */
	private $code;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $deletedAt;

	/**
	 * @ORM\OneToMany(targetEntity=Document::class, mappedBy="sponsor")
	 * @ORM\JoinColumn(nullable=false)
	 * @var ArrayCollection<int, Document>
	 */
	private $documents;

    /**
     * @ORM\OneToMany(targetEntity=Project::class, mappedBy="sponsor")
     * @ORM\JoinColumn(nullable=false)
     * @var ArrayCollection<int, Project>
     */
    private $projects;

	/**
	 * Sponsor constructor.
	 */
	public function __construct()
	{
		$this->documents = new ArrayCollection();
	}

    public function __toString()
    {
        return $this->label;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

	/**
	 * @return string
	 */
	public function getCode(): string
	{
		return $this->code;
	}

	/**
	 * @param string $code
	 */
	public function setCode(string $code): void
	{
		$this->code = $code;
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
			$document->setSponsor($this);
		}

		return $this;
	}

	/**
	 * @param Document $document
	 * @return $this
	 */
	public function removeDocument(Document $document): self
	{
		if ($this->documents->removeElement($document)) {
			// set the owning side to null (unless already changed)
			if ($document->getSponsor() === $this) {
				$document->setSponsor(null);
			}
		}

		return $this;
	}

    /**
     * @return ArrayCollection<int, Project>
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
            $project->setSponsor($this);
        }

        return $this;
    }

    /**
     * @param Project $project
     * @return $this
     */
    public function removeProject(Project $project): self
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getSponsor() === $this) {
                $project->setSponsor(null);
            }
        }

        return $this;
    }

}
