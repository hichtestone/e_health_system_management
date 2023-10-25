<?php

namespace App\ESM\Entity;

use App\ESM\Repository\VersionDocumentTransverseRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=VersionDocumentTransverseRepository::class)
 * @Vich\Uploadable
 */
class VersionDocumentTransverse implements AuditrailableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string",length=255)
     */
    private $version;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string|null
     */
    private $filename1;

    /**
     * @Assert\File
     *     mimeTypes = {"application/pdf", "application/x-pdf"},
     *     mimeTypesMessage = "Veuillez télécharger un PDF valide"
     * )
     * @Vich\UploadableField(mapping="document_transverse_version_file", fileNameProperty="filename1")
     *
     * @var File|null
     */
    private $filename1Vich;

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
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=DocumentTransverse::class, inversedBy="versionDocumentTransverses",cascade={"persist"})
     */
    private $documentTransverse;

	/**
	 * @ORM\OneToMany(targetEntity=ProjectTrailTreatment::class, mappedBy="versionBi")
	 */
	private $projectTrailTreatmentsBi;

	/**
	 * @ORM\OneToMany(targetEntity=ProjectTrailTreatment::class, mappedBy="versionRcp")
	 */
	private $projectTrailTreatmentsRcp;

	/**
	 * VersionDocumentTransverse constructor.
	 */
	public function __construct()
	{
		$this->projectTrailTreatmentsBi  = new ArrayCollection();
		$this->projectTrailTreatmentsRcp = new ArrayCollection();
	}

	/**
	 * @return int|null
	 */
	public function getId(): ?int
    {
        return $this->id;
    }

	/**
	 * @return string|null
	 */
    public function getVersion(): ?string
    {
        return $this->version;
    }

	/**
	 * @param string $version
	 * @return $this
	 */
    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

	/**
	 * @return string|null
	 */
    public function getFilename1(): ?string
    {
        return $this->filename1;
    }

	/**
	 * @param string|null $filename1
	 */
    public function setFilename1(?string $filename1): void
    {
        $this->filename1 = $filename1;
    }

	/**
	 * @return File|null
	 */
    public function getFilename1Vich(): ?File
    {
        return $this->filename1Vich;
    }

	/**
	 * @param File|null $filename1Vich
	 * @return $this
	 */
    public function setFilename1Vich(?File $filename1Vich): VersionDocumentTransverse
    {
        $this->filename1Vich = $filename1Vich;
        if ($this->filename1Vich instanceof UploadedFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

	/**
	 * @return DateTime|null
	 */
    public function getValidStartAt(): ?DateTime
    {
        return $this->validStartAt;
    }

	/**
	 * @param DateTime|null $validStartAt
	 * @return $this
	 */
    public function setValidStartAt(?DateTime $validStartAt): self
    {
        $this->validStartAt = $validStartAt;

        return $this;
    }

	/**
	 * @return DateTime|null
	 */
    public function getValidEndAt(): ?DateTime
    {
        return $this->validEndAt;
    }

	/**
	 * @param DateTime|null $validEndAt
	 * @return $this
	 */
    public function setValidEndAt(?DateTime $validEndAt): self
    {
        $this->validEndAt = $validEndAt;

        return $this;
    }

	/**
	 * @return bool|null
	 */
    public function getIsValid(): ?bool
    {
        return $this->isValid;
    }

	/**
	 * @param bool $isValid
	 * @return $this
	 */
    public function setIsValid(bool $isValid): self
    {
        $this->isValid = $isValid;

        return $this;
    }

	/**
	 * @return \DateTimeInterface|null
	 */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

	/**
	 * @param \DateTimeInterface $createdAt
	 * @return $this
	 */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

	/**
	 * @return \DateTimeInterface|null
	 */
    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

	/**
	 * @param \DateTimeInterface|null $deletedAt
	 * @return $this
	 */
    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

	/**
	 * @return DocumentTransverse|null
	 */
    public function getDocumentTransverse(): ?DocumentTransverse
    {
        return $this->documentTransverse;
    }

	/**
	 * @param DocumentTransverse|null $documentTransverse
	 * @return $this
	 */
    public function setDocumentTransverse(?DocumentTransverse $documentTransverse): self
    {
        $this->documentTransverse = $documentTransverse;

        return $this;
    }

	/**
	 * @return string[]
	 */
    public function getFieldsToBeIgnored(): array
    {
        return ['deletedAt', 'updatedAt'];
    }

	/**
	 * @return string|null
	 */
    public function __toString(): string
    {
        return $this->getVersion();
    }

	/**
	 * @return DateTime|null
	 */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

	/**
	 * @param DateTime $updatedAt
	 * @return $this
	 */
    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

	/**
	 * @return Collection|ProjectTrailTreatment[]
	 */
	public function getProjectTrailTreatmentsBi(): Collection
	{
		return $this->projectTrailTreatmentsBi;
	}

	/**
	 * @param ProjectTrailTreatment $projectTrailTreatment
	 * @return $this
	 */
	public function addProjectTrailTreatmentBi(ProjectTrailTreatment $projectTrailTreatment): self
	{
		if (!$this->projectTrailTreatmentsBi->contains($projectTrailTreatment)) {
			$this->projectTrailTreatmentsBi[] = $projectTrailTreatment;
			$projectTrailTreatment->setVersionBi($this);
		}

		return $this;
	}

	/**
	 * @param ProjectTrailTreatment $projectTrailTreatment
	 * @return $this
	 */
	public function removeProjectTrailTreatmentBi(ProjectTrailTreatment $projectTrailTreatment): self
	{
		if ($this->projectTrailTreatmentsBi->removeElement($projectTrailTreatment)) {
			$projectTrailTreatment->setVersionBi(null);
		}

		return $this;
	}

	/**
	 * @return Collection|ProjectTrailTreatment[]
	 */
	public function getProjectTrailTreatmentsRcp(): Collection
	{
		return $this->projectTrailTreatmentsRcp;
	}

	/**
	 * @param ProjectTrailTreatment $projectTrailTreatment
	 * @return $this
	 */
	public function addProjectTrailTreatmentRcp(ProjectTrailTreatment $projectTrailTreatment): self
	{
		if (!$this->projectTrailTreatmentsRcp->contains($projectTrailTreatment)) {
			$this->projectTrailTreatmentsRcp[] = $projectTrailTreatment;
			$projectTrailTreatment->setVersionRcp($this);
		}

		return $this;
	}

	/**
	 * @param ProjectTrailTreatment $projectTrailTreatment
	 * @return $this
	 */
	public function removeProjectTrailTreatmentRcp(ProjectTrailTreatment $projectTrailTreatment): self
	{
		if ($this->projectTrailTreatmentsRcp->removeElement($projectTrailTreatment)) {
			$projectTrailTreatment->setVersionRcp(null);
		}

		return $this;
	}
}
