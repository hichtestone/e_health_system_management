<?php

namespace App\ETMF\Entity;

use App\ETMF\Repository\TagRepository;
use App\ESM\Entity\User;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
 */
class Tag implements AuditrailableInterface
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
	 * @ORM\ManyToMany(targetEntity=DocumentVersion::class, mappedBy="tags")
     * @ORM\JoinTable(name="document_version_tag")
     *
	 * @var ArrayCollection<int, DocumentVersion>
	 */
	private $documentVersions;

	/**
	 * @ORM\ManyToMany(targetEntity=Document::class, mappedBy="tags")
     * @ORM\JoinTable(name="document_tag")
     *
	 * @var ArrayCollection<int, Document>
	 */
	private $documents;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * @Gedmo\Timestampable(on="create")
	 * @var DateTime
	 */
	private $createdAt;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class)
	 */
	private $createdBy;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * @Gedmo\Timestampable(on="update")
	 * @var DateTime
	 */
	private $updatedAt;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class)
	 * @Gedmo\Blameable(on="update")
	 */
	private $updatedBy;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 *
	 * @var DateTime|null
	 */
	private $deletedAt;

	public function __construct()
	{
		$this->documents 		= new ArrayCollection();
		$this->documentVersions = new ArrayCollection();
	}

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

    /**
     * @return string
     */
	public function __toString(): string
    {
        return $this->name;
    }

	/**
	 * @return string
	 */
	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}

	/**
	 * @return DateTime
	 */
	public function getCreatedAt(): ?DateTime
	{
		return $this->createdAt;
	}

	/**
	 * @param DateTime $createdAt
	 */
	public function setCreatedAt(DateTime $createdAt): void
	{
		$this->createdAt = $createdAt;
	}

	/**
	 * @return User
	 */
	public function getCreatedBy(): ?User
	{
		return $this->createdBy;
	}

	/**
	 * @param User $createdBy
	 */
	public function setCreatedBy(User $createdBy): void
	{
		$this->createdBy = $createdBy;
	}

	/**
	 * @return DateTime
	 */
	public function getUpdatedAt(): DateTime
	{
		return $this->updatedAt;
	}

	/**
	 * @param DateTime $updatedAt
	 */
	public function setUpdatedAt(DateTime $updatedAt): void
	{
		$this->updatedAt = $updatedAt;
	}

	/**
	 * @return User
	 */
	public function getUpdatedBy(): User
	{
		return $this->updatedBy;
	}

	/**
	 * @param User $updatedBy
	 */
	public function setUpdatedBy(User $updatedBy): void
	{
		$this->updatedBy = $updatedBy;
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
     * @return ArrayCollection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    /**
     * @param Document $tag
     * @return $this
     */
    public function addDocument(Document $tag): self
    {
        if (!$this->documents->contains($tag)) {
            $this->documents[] = $tag;
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

    /**
     * @return ArrayCollection<int, DocumentVersion>
     */
    public function getDocumentVersions(): Collection
    {
        return $this->documentVersions;
    }

    /**
     * @param DocumentVersion $documentVersion
     * @return $this
     */
    public function addDocumentVersion(DocumentVersion $documentVersion): self
    {
        if (!$this->documentVersions->contains($documentVersion)) {
            $this->documentVersions[] = $documentVersion;
        }

        return $this;
    }

    /**
     * @param DocumentVersion $documentVersion
     * @return $this
     */
    public function removeDocumentVersion(DocumentVersion $documentVersion): self
    {
        if ($this->documentVersions->contains($documentVersion)) {
            $this->documentVersions->removeElement($documentVersion);
        }

        return $this;
    }

	public function getFieldsToBeIgnored(): array
	{
		return [];
	}
}
