<?php

namespace App\ETMF\Entity;

use App\ESM\Entity\User;
use App\ETMF\Repository\DocumentVersionRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=DocumentVersionRepository::class)
 */
class DocumentVersion implements AuditrailableInterface
{
    public const STATUS_PENDING = 0;
    public const STATUS_PUBLISH = 1;
    public const STATUS_OBSOLETE = 2;
    public const STATUS = [
        self::STATUS_PENDING => 'En attente',
        self::STATUS_PUBLISH => 'En Application',
        self::STATUS_OBSOLETE => 'Obsolète',
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Document::class, inversedBy="documentVersions")
     * @var Document
     */
    private $document;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var mixed
     */
    private $file;

    /**
     * @ORM\Column(type="integer", nullable=false)
     *
     * @var int
     */
    private $numberVersion;

    /**
     * Date d'upload
     *
     * @ORM\Column(type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     * @var DateTime
     */
    private $createdAt;

    /**
     * Uploadé par
     *
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="documentVersionsCreatedBy")
     * @var User|null
     */
    private $createdBy;

    /**
     * Auteur
     *
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="documentVersionsAuthor")
     * @ORM\JoinColumn(nullable=true)
     * @var User|null
     */
    private $author;

    /**
     * @ORM\Column(type="integer", nullable=false)
     *
     * @var int|null
     */
    private $status;

    /**
     * Date de mise en applicatio
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime|null
     */
    private $applicationAt;

    /**
     * Date d'expiration
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime|null
     */
    private $expiredAt;

    /**
     * Date de signature du document
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime|null
     */
    private $signedAt;

    /**
     * Date AQ
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime|null
     */
    private $validatedQaAt;

    /**
     * AQ réalisé par
     *
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="documentVersionsValidatedQaBy")
     * @ORM\JoinColumn(nullable=true)
     * @var User|null
     */
    private $validatedQaBy;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="documentVersions")
     * @ORM\JoinTable(name="document_version_tag")
     *
     * @var ArrayCollection<int, Tag>
     */
    private $tags;

    /**
     * DocumentVersion constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return (string) $this->numberVersion;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getFieldsToBeIgnored(): array
    {
        return [];
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     * @return $this
     */
    public function setFile($file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return Document|null
     */
    public function getDocument(): ?Document
    {
        return $this->document;
    }

    /**
     * @param Document|null $document
     */
    public function setDocument(?Document $document): void
    {
        $this->document = $document;
    }

    /**
     * @return int
     */
    public function getNumberVersion(): int
    {
        return $this->numberVersion;
    }

    /**
     * @param int $numberVersion
     */
    public function setNumberVersion(int $numberVersion): void
    {
        $this->numberVersion = $numberVersion;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
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
     * @return User|null
     */
    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    /**
     * @param User|null $createdBy
     */
    public function setCreatedBy(?User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return User|null
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User|null $author
     */
    public function setAuthor(?User $author): void
    {
        $this->author = $author;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int|null $status
     */
    public function setStatus(?int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return DateTime|null
     */
    public function getApplicationAt(): ?DateTime
    {
        return $this->applicationAt;
    }

    /**
     * @param DateTime|null $applicationAt
     */
    public function setApplicationAt(?DateTime $applicationAt): void
    {
        $this->applicationAt = $applicationAt;
    }

    /**
     * @return DateTime|null
     */
    public function getExpiredAt(): ?DateTime
    {
        return $this->expiredAt;
    }

    /**
     * @param DateTime|null $expiredAt
     */
    public function setExpiredAt(?DateTime $expiredAt): void
    {
        $this->expiredAt = $expiredAt;
    }

    /**
     * @return DateTime|null
     */
    public function getSignedAt(): ?DateTime
    {
        return $this->signedAt;
    }

    /**
     * @param DateTime|null $signedAt
     */
    public function setSignedAt(?DateTime $signedAt): void
    {
        $this->signedAt = $signedAt;
    }

    /**
     * @return DateTime|null
     */
    public function getValidatedQaAt(): ?DateTime
    {
        return $this->validatedQaAt;
    }

    /**
     * @param DateTime|null $validatedQaAt
     */
    public function setValidatedQaAt(?DateTime $validatedQaAt): void
    {
        $this->validatedQaAt = $validatedQaAt;
    }

    /**
     * @return User|null
     */
    public function getValidatedQaBy(): ?User
    {
        return $this->validatedQaBy;
    }

    /**
     * @param User|null $validatedQaBy
     */
    public function setValidatedQaBy(?User $validatedQaBy): void
    {
        $this->validatedQaBy = $validatedQaBy;
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
