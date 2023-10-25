<?php

namespace App\ESM\Entity;

use App\ESM\Repository\DeviationSystemActionRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeviationSystemActionRepository::class)
 */
class DeviationSystemAction implements AuditrailableInterface
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 *
	 * @var int|null
	 */
	private $status;

	/**
	 * @ORM\Column(type="integer", nullable=false)
	 *
	 * @var int
	 */
	private $typeAction;

	/**
	 * @ORM\ManyToOne(targetEntity=DeviationSystem::class, inversedBy="actions")
	 * @ORM\JoinColumn(nullable=false)
	 *
	 * @var DeviationSystem
	 */
	private $deviationSystem;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 *
	 * @var string|null
	 */
	private $description;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 *
	 * @var DateTime|null
	 */
	private $applicationAt;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 *
	 * @var DateTime|null
	 */
	private $doneAt;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="deviationSystemActions")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
	 *
	 * @var User
	 */
	private $intervener;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 *
	 * @var string|null
	 */
	private $comment;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 *
	 * @var DateTime|null
	 */
	private $deletedAt;

	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $isDone = false;

	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 *
	 * @var bool
	 */
	private $isCrex = false;

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return (string) $this->getDeviationSystem()->getCode();
	}

	/**
	 * @return int
	 */
	public function getStatus(): ?int
	{
		return $this->status;
	}

	/**
	 * @param int|null $status
	 * @return $this
	 */
	public function setStatus(?int $status): self
	{
		$this->status = $status;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getTypeAction(): ?int
	{
		return $this->typeAction;
	}

	/**
	 * @param int $typeAction
	 * @return $this
	 */
	public function setTypeAction(int $typeAction): self
	{
		$this->typeAction = $typeAction;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getTypeManager(): ?int
	{
		return $this->typeManager;
	}

	/**
	 * @param int $typeManager
	 * @return $this
	 */
	public function setTypeManager(int $typeManager): self
	{
		$this->typeManager = $typeManager;

		return $this;
	}

	/**
	 * @return DeviationSystem
	 */
	public function getDeviationSystem(): DeviationSystem
	{
		return $this->deviationSystem;
	}

	/**
	 * @param DeviationSystem $deviationSystem
	 */
	public function setDeviationSystem(DeviationSystem $deviationSystem): void
	{
		$this->deviationSystem = $deviationSystem;
	}

	/**
	 * @return string|null
	 *
	 */
	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @param string|null $description
	 *
	 * @return $this
	 */
	public function setDescription(string $description): self
	{
		$this->description = $description;

		return $this;
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
	 * @return $this
	 */
	public function setApplicationAt(?DateTime $applicationAt): self
	{
		$this->applicationAt = $applicationAt;

		return $this;
	}

	/**
	 * @return DateTime|null
	 */
	public function getDoneAt(): ?DateTime
	{
		return $this->doneAt;
	}

	/**
	 * @param DateTime|null $doneAt
	 * @return $this
	 */
	public function setDoneAt(?DateTime $doneAt): self
	{
		$this->doneAt = $doneAt;

		return $this;
	}

	/**
	 * @return Interlocutor|null
	 */
	public function getInterlocutor(): ?Interlocutor
	{
		return $this->interlocutor;
	}

	/**
	 * @param Interlocutor|null $interlocutor
	 */
	public function setInterlocutor(?Interlocutor $interlocutor): void
	{
		$this->interlocutor = $interlocutor;
	}

	/**
	 * @return User|null
	 */
	public function getIntervener(): ?User
	{
		return $this->intervener;
	}

	/**
	 * @param User|null $user
	 */
	public function setIntervener(?User $user): void
	{
		$this->intervener = $user;
	}

	/**
	 * @return string|null
	 */
	public function getComment(): ?string
	{
		return $this->comment;
	}

	/**
	 * @param string|null $comment
	 *
	 * @return $this
	 */
	public function setComment(string $comment): self
	{
		$this->comment = $comment;

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
	public function getIsDone(): bool
	{
		return $this->isDone;
	}

	/**
	 * @param bool $isDone
	 */
	public function setIsDone(bool $isDone): void
	{
		$this->isDone = $isDone;
	}

	/**
	 * @return bool
	 */
	public function getIsCrex(): bool
	{
		return $this->isCrex;
	}

	/**
	 * @param bool $isCrex
	 */
	public function setIsCrex(bool $isCrex): void
	{
		$this->isCrex = $isCrex;
	}

	/**
	 * @return array
	 */
	public function getFieldsToBeIgnored(): array
	{
		return [];
	}
}
