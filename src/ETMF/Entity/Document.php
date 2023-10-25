<?php

namespace App\ETMF\Entity;

use App\ESM\Entity\Center;
use App\ESM\Entity\DropdownList\Country;
use App\ESM\Entity\DropdownList\Sponsor;
use App\ETMF\Entity\DropdownList\DocumentLevel;
use App\ETMF\Repository\DocumentRepository;
use App\ESM\Entity\Project;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 */
class Document implements AuditrailableInterface
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
     * @var string
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=DocumentLevel::class, inversedBy="documents")
     * @ORM\JoinTable(name="document_artefactLevel")
     *
     * @var ArrayCollection<int, DocumentLevel>
     */
    private $documentLevels;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Zone::class, inversedBy="documents")
     * @ORM\JoinColumn(nullable=true)
     * @var Zone|null
     */
    private $zone;

    /**
     * @ORM\ManyToOne(targetEntity=Section::class, inversedBy="documents")
     * @ORM\JoinColumn(nullable=true)
     * @var Section|null
     */
    private $section;

    /**
     * @ORM\ManyToOne(targetEntity=Artefact::class, inversedBy="documents")
     * @ORM\JoinColumn(nullable=true)
     * @var Artefact|null
     */
    private $artefact;

    /**
     * @ORM\ManyToOne(targetEntity=Sponsor::class, inversedBy="documents")
     * @ORM\JoinColumn(nullable=true)
     * @var Sponsor|null
     */
    private $sponsor;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="documents")
     * @ORM\JoinColumn(nullable=true)
     * @var Project|null
     */
    private $project;

    /**
     * @ORM\OneToMany(targetEntity=DocumentVersion::class, mappedBy="document")
     *
     * @var ArrayCollection<int, DocumentVersion>
     */
    private $documentVersions;

    /**
     * @ORM\ManyToMany(targetEntity=Country::class, inversedBy="documents")
     * @ORM\JoinTable(name="document_country")
     *
     * @var ArrayCollection<int, Country>
     */
    private $countries;

    /**
     * @ORM\ManyToMany(targetEntity=Center::class, inversedBy="documents")
     * @ORM\JoinTable(name="document_center")
     *
     * @var ArrayCollection<int, Center>
     */
    private $centers;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="documents")
     * @ORM\JoinTable(name="document_tag")
     *
     * @var ArrayCollection<int, Tag>
     */
    private $tags;

    /**
     * Document constructor.
     */
    public function __construct()
    {
        $this->documentVersions = new ArrayCollection();
        $this->countries 		= new ArrayCollection();
        $this->centers 			= new ArrayCollection();
        $this->documentLevels 	= new ArrayCollection();
        $this->tags 			= new ArrayCollection();
    }

    public function getFieldsToBeIgnored(): array
    {
        return [];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->getName() ?? '';
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return ArrayCollection<int, DocumentLevel>
     */
    public function getDocumentLevels(): Collection
    {
        return $this->documentLevels;
    }

    /**
     * @param DocumentLevel $documentLevel
     * @return $this
     */
    public function addDocumentLevel(DocumentLevel $documentLevel): self
    {
        if (!$this->documentLevels->contains($documentLevel)) {
            $this->documentLevels[] = $documentLevel;
        }

        return $this;
    }

    /**
     * @param DocumentLevel $documentLevel
     * @return $this
     */
    public function removeDocumentLevel(DocumentLevel $documentLevel): self
    {
        if ($this->documentLevels->contains($documentLevel)) {
            $this->documentLevels->removeElement($documentLevel);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Zone|null
     */
    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    /**
     * @param Zone|null $zone
     */
    public function setZone(?Zone $zone): void
    {
        $this->zone = $zone;
    }

    /**
     * @return Section|null
     */
    public function getSection(): ?Section
    {
        return $this->section;
    }

    /**
     * @param Section|null $section
     */
    public function setSection(?Section $section): void
    {
        $this->section = $section;
    }

    /**
     * @return Artefact|null
     */
    public function getArtefact(): ?Artefact
    {
        return $this->artefact;
    }

    /**
     * @param Artefact|null $artefact
     */
    public function setArtefact(?Artefact $artefact): void
    {
        $this->artefact = $artefact;
    }

    /**
     * @return Sponsor|null
     */
    public function getSponsor(): ?Sponsor
    {
        return $this->sponsor;
    }

    /**
     * @param Sponsor|null $sponsor
     */
    public function setSponsor(?Sponsor $sponsor): void
    {
        $this->sponsor = $sponsor;
    }

    /**
     * @return Project|null
     */
    public function getProject(): ?Project
    {
        return $this->project;
    }

    /**
     * @param Project|null $project
     */
    public function setProject(?Project $project): void
    {
        $this->project = $project;
    }

    /**
     * @param DocumentVersion $documentVersion
     * @return $this
     */
    public function addDocumentVersion(DocumentVersion $documentVersion): self
    {
        if (!$this->documentVersions->contains($documentVersion)) {
            $this->documentVersions[] = $documentVersion;
            $documentVersion->setDocument($this);
        }

        return $this;
    }

    /**
     * @param DocumentVersion $documentVersion
     * @return $this
     */
    public function removeDocumentVersion(DocumentVersion $documentVersion): self
    {
        if ($this->documentVersions->removeElement($documentVersion)) {
            // set the owning side to null (unless already changed)
            if ($documentVersion->getDocument() === $this) {
                $documentVersion->setDocument(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection<int, Country>
     */
    public function getCountries(): Collection
    {
        return $this->countries;
    }

    public function addCountry(Country $country): self
    {
        if (!$this->countries->contains($country)) {
            $this->countries[] = $country;
        }

        return $this;
    }

    public function removeCountry(Country $country): self
    {
        if ($this->countries->contains($country)) {
            $this->countries->removeElement($country);
        }

        return $this;
    }

    /**
     * @return ArrayCollection<int, Center>
     */
    public function getCenters(): Collection
    {
        return $this->centers;
    }

    /**
     * @param Center $center
     * @return $this
     */
    public function addCenter(Center $center): self
    {
        if (!$this->centers->contains($center)) {
            $this->centers[] = $center;
        }

        return $this;
    }

    /**
     * @param Center $center
     * @return $this
     */
    public function removeCenter(Center $center): self
    {
        if ($this->centers->contains($center)) {
            $this->centers->removeElement($center);
        }

        return $this;
    }

    /**
     * @return ArrayCollection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * @param Tag $tag
     * @return $this
     */
    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    /**
     * @param Tag $tag
     * @return $this
     */
    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }
}
