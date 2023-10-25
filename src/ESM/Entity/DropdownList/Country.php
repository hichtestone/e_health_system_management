<?php

namespace App\ESM\Entity\DropdownList;

use App\ESM\Entity\Project;
use App\ESM\Repository\CountryRepository;
use App\ETMF\Entity\Document;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CountryRepository::class)
 * @ORM\Table(name="dl_country")
 */
class Country
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     * @Groups({"country"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     * @Groups({"country"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     * @Groups({"country"})
     */
    private $nameEnglish;

    /**
     * @ORM\Column(type="string", length=2)
     *
     * @var string
     * @Groups({"country"})
     */
    private $code;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $deletedAt;

    /**
     * @ORM\OneToMany(targetEntity=TypeSubmissionRegulatory::class, mappedBy="country")
     *
     * @var ArrayCollection<int, TypeSubmissionRegulatory>
     */
    private $typeSubmissionRegulatory;

    /**
     * @ORM\ManyToMany(targetEntity=Project::class, mappedBy="countries")
     *
     * @var ArrayCollection<int, Project>
     */
    private $projects;

    /**
     * @ORM\ManyToMany(targetEntity=Document::class, mappedBy="countries")
     * @ORM\JoinTable(name="document_country")
     *
     * @var ArrayCollection<int, Document>
     */
    private $documents;


    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->documents = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->code;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNameEnglish(): ?string
    {
        return $this->nameEnglish;
    }

    public function setNameEnglish(string $nameEnglish): self
    {
        $this->nameEnglish = $nameEnglish;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

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
     * @return ArrayCollection<int, TypeSubmissionRegulatory>
     */
    public function getTypeSubmissionRegulatories(): Collection
    {
        return $this->typeSubmissionRegulatory;
    }

    public function addTypeSubmissionRegulatories(TypeSubmissionRegulatory $typeSubmissionRegulatory): self
    {
        if (!$this->typeSubmissionRegulatory->contains($typeSubmissionRegulatory)) {
            $this->typeSubmissionRegulatory[] = $typeSubmissionRegulatory;
        }

        return $this;
    }

    public function removeTypeSubmissionRegulatories(TypeSubmissionRegulatory $typeSubmissionRegulatory): self
    {
        if ($this->typeSubmissionRegulatory->contains($typeSubmissionRegulatory)) {
            $this->typeSubmissionRegulatory->removeElement($typeSubmissionRegulatory);
        }

        return $this;
    }

    public function getProjects()
    {
        return $this->projects;
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
