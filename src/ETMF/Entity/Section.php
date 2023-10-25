<?php

namespace App\ETMF\Entity;

use App\ETMF\Repository\SectionRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=SectionRepository::class)
 */
class Section implements AuditrailableInterface
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=255)
	 * @var string
	 */
	private $name;

	/**
	 * @ORM\Column(type="smallint", nullable=false)
	 * @var int|null
	 */
	private $code;

	/**
	 * @ORM\ManyToOne(targetEntity=Zone::class, inversedBy="sections")
	 * @ORM\JoinColumn(nullable=true)
	 * @var Zone
	 */
	private $zone;

	/**
	 * @ORM\OneToMany(targetEntity=Artefact::class, mappedBy="section")
	 * @var ArrayCollection<int, Artefact>
	 */
	private $artefacts;

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
	 * @var DateTime
	 */
	private $updatedAt;

	/**
	 * @Gedmo\Blameable(on="update")
	 * @var string
	 */
	private $updatedBy;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * @var DateTime|null
	 */
	private $deletedAt;

	/**
	 * Section constructor.
	 */
	public function __construct()
	{
		$this->artefacts = new ArrayCollection();
	}

	/**
	 * @return string[]
	 */
	public function getFieldsToBeIgnored(): array
	{
		return [];
	}

	/**
	 * @return string|null
	 */
	public function __toString()
	{
		return $this->getName();
	}

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
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
	 * @return ArrayCollection<int, Artefact>
	 */
	public function getArtefacts(): Collection
	{
		return $this->artefacts;
	}

	/**
	 * @param Artefact $artefact
	 * @return $this
	 */
	public function addArtefact(Artefact $artefact): self
	{
		if (!$this->artefacts->contains($artefact)) {
			$this->artefacts[] = $artefact;
			$artefact->setSection($this);
		}

		return $this;
	}

	/**
	 * @param Artefact $artefact
	 * @return $this
	 */
	public function removeArtefact(Artefact $artefact): self
	{
		if ($this->artefacts->removeElement($artefact)) {
			// set the owning side to null (unless already changed)
			if ($artefact->getSection() === $this) {
				$artefact->setSection(null);
			}
		}

		return $this;
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
	 * @return DateTime
	 */
	public function getUpdatedAt(): ?DateTime
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
	 * @return string
	 */
	public function getUpdatedBy(): ?string
	{
		return $this->updatedBy;
	}

	/**
	 * @param string $updatedBy
	 */
	public function setUpdatedBy(string $updatedBy): void
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
	 * @return bool
	 */
	public function hasDocuments(): bool
	{
		foreach ($this->artefacts as $artefact) {

			if ($artefact->hasDocuments()) {
				return true;
			}
		}

		return false;
	}
}
