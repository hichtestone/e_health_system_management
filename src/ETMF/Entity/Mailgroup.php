<?php

namespace App\ETMF\Entity;

use App\ESM\Entity\User;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use App\ETMF\Repository\MailgroupRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=MailgroupRepository::class)
 * @UniqueEntity("name")
 */
class Mailgroup implements AuditrailableInterface
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=255, nullable=false)
	 * @var string|null
	 */
	private $name;

	/**
	 * @ORM\ManyToMany(targetEntity=User::class, inversedBy="mailgroups")
	 * @ORM\JoinTable(name="users_mailgroups")
	 * @var Collection<int, User>
	 */
	private $users;

	/**
	 * @ORM\ManyToMany(targetEntity=Artefact::class, mappedBy="mailgroups")
	 * @ORM\JoinTable(name="artefact_mailGroup")
	 * @var ArrayCollection<int, Artefact>
	 */
	private $artefacts;

	/**
	 * @ORM\Column(type="datetime", nullable=false)
	 * @Gedmo\Timestampable(on="create")
	 */
	private $createdAt;

	/**
	 * @Gedmo\Blameable(on="create")
	 * @var string
	 */
	private $createdBy;

	/**
	 * @ORM\Column(name="updated", type="datetime", nullable=true)
	 * @Gedmo\Timestampable(on="update")
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

	public function __construct()
	{
		$this->users = new ArrayCollection();
		$this->artefacts = new ArrayCollection();
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
	 * @return Collection<int, User>
	 */
	public function getUsers(): Collection
	{
		return $this->users;
	}

	/**
	 * @return $this
	 */
	public function addUser(User $user): self
	{
		if (!$this->users->contains($user)) {
			$this->users[] = $user;
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	public function removeUser(User $user): self
	{
		if ($this->users->contains($user)) {
			$this->users->removeElement($user);
		}

		return $this;
	}

	/**
	 * @return Collection
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
		}

		return $this;
	}

	/**
	 * @param Artefact $artefact
	 * @return $this
	 */
	public function removeArtefact(Artefact $artefact): self
	{
		if ($this->artefacts->contains($artefact)) {
			$this->artefacts->removeElement($artefact);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCreatedAt()
	{
		return $this->createdAt;
	}

	/**
	 * @param mixed $createdAt
	 */
	public function setCreatedAt($createdAt): void
	{
		$this->createdAt = $createdAt;
	}

	/**
	 * @return mixed
	 */
	public function getUpdatedAt()
	{
		return $this->updatedAt;
	}

	/**
	 * @param mixed $updatedAt
	 */
	public function setUpdatedAt($updatedAt): void
	{
		$this->updatedAt = $updatedAt;
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
	 * @return string
	 */
	public function getUpdatedBy(): string
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
	 */
	public function setDeletedAt(?DateTime $deletedAt): void
	{
		$this->deletedAt = $deletedAt;
	}

	public function getFieldsToBeIgnored(): array
	{
		return [];
	}
}
