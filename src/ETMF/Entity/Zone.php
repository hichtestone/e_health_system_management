<?php

namespace App\ETMF\Entity;

use App\ETMF\Repository\ZoneRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=ZoneRepository::class)
 */
class Zone implements AuditrailableInterface
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 *
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=255, nullable=false)
	 *
	 * @var string|null
	 */
	private $name;

	/**
	 * @ORM\Column(type="smallint", nullable=false)
	 *
	 * @var int|null
	 */
	private $code;

	/**
	 * @ORM\OneToMany(targetEntity=Section::class, mappedBy="zone")
	 *
	 * @var ArrayCollection<int, Section>
	 */
	private $sections;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
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
	 *
	 * @var DateTime|null
	 */
	private $deletedAt;

	/**
	 * Zone constructor.
	 */
	public function __construct()
	{
		$this->sections = new ArrayCollection();
	}

	/**
	 * @return string[]
	 */
	public function getFieldsToBeIgnored(): array
	{
		return [''];
	}

	/**
	 * @return string|null
	 */
	public function __toString()
	{
		return $this->getName();
	}

    /**
     * @return mixed
     */
	public function getId()
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
	 * @return ArrayCollection<int, Section>
	 */
	public function getSections(): Collection
	{
		return $this->sections;
	}

	/**
	 * @param Section $section
	 * @return $this
	 */
	public function addSection(Section $section): self
	{
		if (!$this->sections->contains($section)) {
			$this->sections[] = $section;
			$section->setZone($this);
		}

		return $this;
	}

	/**
	 * @param Section $section
	 * @return $this
	 */
	public function removeSection(Section $section): self
	{
		if ($this->sections->removeElement($section)) {
			// set the owning side to null (unless already changed)
			if ($section->getZone() === $this) {
				$section->setZone(null);
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
		foreach ($this->sections as $section) {

			if ($section->hasDocuments()) {
				return true;
			}
		}

		return false;
	}
}
