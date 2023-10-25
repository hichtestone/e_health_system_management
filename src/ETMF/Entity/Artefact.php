<?php

namespace App\ETMF\Entity;

use App\ESM\Service\AuditTrail\AuditrailableInterface;
use App\ETMF\Entity\DropdownList\ArtefactLevel;
use App\ETMF\Repository\ArtefactRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ArtefactRepository::class)
 * @UniqueEntity("name")
 */
class Artefact implements AuditrailableInterface
{
	public const EXTENSION_PDF   	= 0;
	public const EXTENSION_CSV   	= 1;
	public const EXTENSION_EXCEL 	= 2;
	public const EXTENSION_WORD 	= 3;
	public const EXTENSIONS = [
		self::EXTENSION_PDF   => 'pdf',
		self::EXTENSION_CSV   => 'csv',
		self::EXTENSION_EXCEL => 'excel',
		self::EXTENSION_WORD  => 'word',
	];

	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 * @var int
     * @Groups({"artefact"})
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=255, nullable=false)
	 * @var string|null
     * @Groups({"artefact"})
	 */
	private $name;

	/**
	 * @ORM\Column(type="smallint", nullable=false)
	 * @var int|null
     * @Groups({"artefact"})
	 */
	private $code;

    /**
     * @ORM\ManyToMany(targetEntity=ArtefactLevel::class, inversedBy="artefacts")
     * @ORM\JoinTable(name="artefact_artefactLevel")
     * @var ArrayCollection<int, ArtefactLevel>
     * @Groups({"artefact"})
     */
    private $artefactLevels;

	/**
	 * @ORM\Column(type="json")
	 * @var array
	 */
	private $extension = [];

	/**
	 * @ORM\Column(type="smallint", nullable=true)
	 * @var int|null
	 */
	private $delayExpired;

	/**
	 * @ORM\ManyToOne(targetEntity=Section::class, inversedBy="artefacts")
	 * @ORM\JoinColumn(nullable=true)
	 * @var Section
	 */
	private $section;

	/**
	 * @ORM\ManyToMany(targetEntity=Mailgroup::class, inversedBy="artefacts")
	 * @ORM\JoinTable(name="artefact_mailGroup")
	 * @var ArrayCollection<int, Mailgroup>
	 */
	private $mailgroups;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * @var DateTime|null
	 */
	private $deletedAt;

	/**
	 * @ORM\OneToMany(targetEntity=Document::class, mappedBy="artefact")
	 * @ORM\JoinColumn(nullable=false)
	 * @var ArrayCollection<int, Document>
	 */
	private $documents;

	/**
	 * @ORM\Column(type="datetime", nullable=false)
	 * @Gedmo\Timestampable(on="create")
	 * @var DateTime
	 */
	private $createdAt;

	/**
	 * @Gedmo\Blameable(on="create")
	 * @var string
	 */
	private $createdBy;

	/**
	 * @Gedmo\Timestampable(on="update")
	 * @var DateTime|null
	 */
	private $updatedAt;

	/**
	 * @Gedmo\Blameable(on="update")
	 * @var string
	 */
	private $updatedBy;

	/**
	 * Artefact constructor.
	 */
	public function __construct()
	{
		$this->mailgroups 		= new ArrayCollection();
		$this->documents 		= new ArrayCollection();
		$this->artefactLevels 	= new ArrayCollection();
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
	 * @return int|null
	 */
	public function getCode(): ?int
	{
		return $this->code;
	}

	/**
	 * @param int|null $code
	 */
	public function setCode(?int $code): void
	{
		$this->code = $code;
	}

    /**
     * @return ArrayCollection<int, ArtefactLevel>
     */
    public function getArtefactLevels(): Collection
    {
        return $this->artefactLevels;
    }

    /**
     * @param ArtefactLevel $artefactLevel
     * @return $this
     */
    public function addArtefactLevel(ArtefactLevel $artefactLevel): self
    {
        if (!$this->artefactLevels->contains($artefactLevel)) {
            $this->artefactLevels[] = $artefactLevel;
        }

        return $this;
    }

    /**
     * @param ArtefactLevel $artefactLevel
     * @return $this
     */
    public function removeArtefactLevel(ArtefactLevel $artefactLevel): self
    {
        if ($this->artefactLevels->contains($artefactLevel)) {
            $this->artefactLevels->removeElement($artefactLevel);
        }

        return $this;
    }

	/**
	 * @return array
	 */
	public function getExtension(): array
	{
		return $this->extension;
	}

	/**
	 * @param array $extension
	 */
	public function setExtension(array $extension): void
	{
		$this->extension = $extension;
	}

	/**
	 * @return int|null
	 */
	public function getDelayExpired(): ?int
	{
		return $this->delayExpired;
	}

	/**
	 * @param int|null $delayExpired
	 */
	public function setDelayExpired(?int $delayExpired): void
	{
		$this->delayExpired = $delayExpired;
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
	 * @return Collection
	 */
	public function getMailgroups(): Collection
	{
		return $this->mailgroups;
	}

	/**
	 * @param Mailgroup $mailgroup
	 * @return $this
	 */
	public function addMailgroup(Mailgroup $mailgroup): self
	{
		if (!$this->mailgroups->contains($mailgroup)) {
			$this->mailgroups[] = $mailgroup;
		}

		return $this;
	}

	/**
	 * @param Mailgroup $mailgroup
	 * @return $this
	 */
	public function removeMailgroup(Mailgroup $mailgroup): self
	{
		if ($this->mailgroups->contains($mailgroup)) {
			$this->mailgroups->removeElement($mailgroup);
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
			$document->setArtefact($this);
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
			if ($document->getArtefact() === $this) {
				$document->setArtefact(null);
			}
		}

		return $this;
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
	 */
	public function setDeletedAt(?DateTime $deletedAt): void
	{
		$this->deletedAt = $deletedAt;
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
	 * @return string
	 */
	public function getCreatedBy(): ?string
	{
		return $this->createdBy;
	}

	/**
	 * @param string $createdBy
	 */
	public function setCreatedBy(string $createdBy): void
	{
		$this->createdBy = $createdBy;
	}

	/**
	 * @return DateTime|null
	 */
	public function getUpdatedAt(): ?DateTime
	{
		return $this->updatedAt;
	}

	/**
	 * @param DateTime|null $updatedAt
	 */
	public function setUpdatedAt(?DateTime $updatedAt): void
	{
		$this->updatedAt = $updatedAt;
	}

	/**
	 * @return string|null
	 */
	public function getUpdatedBy(): ?string
	{
		return $this->updatedBy;
	}

	/**
	 * @param string|null $updatedBy
	 */
	public function setUpdatedBy(?string $updatedBy): void
	{
		$this->updatedBy = $updatedBy;
	}

	/**
	 * @return bool
	 */
	public function hasDocuments(): bool
	{
		return !$this->documents->isEmpty();
	}

    /**
     * @return array
     */
	public function getFieldsToBeIgnored(): array
	{
		return [];
	}
}
