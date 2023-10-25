<?php

namespace App\ESM\Entity;

use App\ESM\Repository\DocumentTransverseRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=DocumentTransverseRepository::class)
 * @Vich\Uploadable
 */
class DocumentTransverse implements AuditrailableInterface
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
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string|null
     */
    private $filename;

    /**
     * @Vich\UploadableField(mapping="document_transverse_file", fileNameProperty="filename")
     *
     * @var File|null
     */
    private $filenameVich;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $validStartAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $validEndAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isValid;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=VersionDocumentTransverse::class, mappedBy="documentTransverse", cascade={"persist"})
     */
    private $versionDocumentTransverses;

    /**
     * @ORM\ManyToOne(targetEntity=PorteeDocumentTransverse::class, inversedBy="documentTransverses")
     */
    private $porteeDocument;

    /**
     * @ORM\ManyToOne(targetEntity=TypeDocumentTransverse::class, inversedBy="documentTransverses")
     */
    private $TypeDocument;

    /**
     * @ORM\ManyToOne(targetEntity=Institution::class, inversedBy="documentTransverses")
     */
    private $institution;

    /**
     * @ORM\ManyToOne(targetEntity=Interlocutor::class, inversedBy="documentTransverses")
     */
    private $interlocutor;

    /**
     * @ORM\ManyToOne(targetEntity=Drug::class, inversedBy="documentTransverses")
     */
    private $drug;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $drugFirstVersion;

    public function __construct()
    {
        $this->versionDocumentTransverses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString()
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

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): void
    {
        $this->filename = $filename;
    }

    public function getFilenameVich(): ?File
    {
        return $this->filenameVich;
    }

    public function setFilenameVich(?File $filenameVich): DocumentTransverse
    {
        $this->filenameVich = $filenameVich;
        if ($this->filenameVich instanceof UploadedFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getValidStartAt(): ?DateTime
    {
        return $this->validStartAt;
    }

    public function setValidStartAt(?DateTime $validStartAt): self
    {
        $this->validStartAt = $validStartAt;

        return $this;
    }

    public function getValidEndAt(): ?DateTime
    {
        return $this->validEndAt;
    }

    public function setValidEndAt(?DateTime $validEndAt): self
    {
        $this->validEndAt = $validEndAt;

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

    /**
     * @return Collection|VersionDocumentTransverse[]
     */
    public function getVersionDocumentTransverses(): Collection
    {
        return $this->versionDocumentTransverses;
    }

    public function addVersionDocumentTransverse(VersionDocumentTransverse $versionDocumentTransverse): self
    {
        if (!$this->versionDocumentTransverses->contains($versionDocumentTransverse)) {
            $this->versionDocumentTransverses[] = $versionDocumentTransverse;
            $versionDocumentTransverse->setDocumentTransverse($this);
        }

        return $this;
    }

    public function removeVersionDocumentTransverse(VersionDocumentTransverse $versionDocumentTransverse): self
    {
        if ($this->versionDocumentTransverses->removeElement($versionDocumentTransverse)) {
            // set the owning side to null (unless already changed)
            if ($versionDocumentTransverse->getDocumentTransverse() === $this) {
                $versionDocumentTransverse->setDocumentTransverse(null);
            }
        }

        return $this;
    }

    public function getPorteeDocument(): ?PorteeDocumentTransverse
    {
        return $this->porteeDocument;
    }

    public function setPorteeDocument(?PorteeDocumentTransverse $porteeDocument): self
    {
        $this->porteeDocument = $porteeDocument;

        return $this;
    }

    public function getTypeDocument(): ?TypeDocumentTransverse
    {
        return $this->TypeDocument;
    }

    public function setTypeDocument(?TypeDocumentTransverse $TypeDocument): self
    {
        $this->TypeDocument = $TypeDocument;

        return $this;
    }

    public function getInterlocutor(): ?Interlocutor
    {
        return $this->interlocutor;
    }

    public function setInterlocutor(?Interlocutor $interlocutor): self
    {
        $this->interlocutor = $interlocutor;

        return $this;
    }

    public function getDrug(): ?Drug
    {
        return $this->drug;
    }

    public function setDrug(?Drug $drug): self
    {
        $this->drug = $drug;

        return $this;
    }

    public function getInstitution(): ?Institution
    {
        return $this->institution;
    }

    public function setInstitution(?Institution $institution): self
    {
        $this->institution = $institution;

        return $this;
    }

    public function getFieldsToBeIgnored(): array
    {
        return ['drug', 'versionDocumentTransverses', 'updatedAt'];
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDrugFirstVersion(): ?string
    {
        return $this->drugFirstVersion;
    }

    public function setDrugFirstVersion(?string $drugFirstVersion): self
    {
        $this->drugFirstVersion = $drugFirstVersion;

        return $this;
    }
}
