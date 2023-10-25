<?php

namespace App\ESM\Entity;

use App\ESM\Repository\DeviationSystemReviewRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeviationSystemReviewRepository::class)
 */
class DeviationSystemReview implements AuditrailableInterface
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity=DeviationSystem::class, inversedBy="reviews")
	 * @ORM\JoinColumn(name="deviation_system_id", referencedColumnName="id")
	 *
	 * @var DeviationSystem
	 */
	private $deviationSystem;

	/**
	 * @ORM\Column(type="smallint", nullable=true)
	 *
	 * @var int|null
	 */
	private $number;

	/**
	 * @ORM\Column(type="smallint", nullable=true)
	 *
	 * @var int|null
	 */
	private $numberCrex;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $isCrex;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 *
	 * @var DateTime|null
	 */
	private $createAt;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 *
	 * @var DateTime|null
	 */
	private $doneAt;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 *
	 * @var DateTime|null
	 */
	private $validatedAt;

	/**
	 * @ORM\Column(type="integer", nullable=false)
	 */
	private $status;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $type;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 *
	 * @var string|null
	 */
	private $comment;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="deviationReviews")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
	 *
	 * @var
	 */
	private $reader;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 *
	 * @var DateTime|null
	 */
	private $deletedAt;

	/**
	 * @ORM\OneToMany(targetEntity=DeviationSystemReviewAction::class, mappedBy="deviationSystemReview")
	 *
	 * @var ArrayCollection<int, DeviationSystemReviewAction>
	 */
	private $reviewActions;

	public function __construct()
	{
		$this->reviewActions = new ArrayCollection();
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	public function __toString(): string
	{
		return (string) $this->getNumber();
	}

	public function getDeviationSystem(): ?DeviationSystem
	{
		return $this->deviationSystem;
	}

	public function setDeviationSystem(?DeviationSystem $deviationSystem): void
	{
		$this->deviationSystem = $deviationSystem;
	}

	public function getNumber(): ?int
	{
		return $this->number;
	}

	public function setNumber(?int $number): void
	{
		$this->number = $number;
	}

	public function getNumberCrex(): ?int
	{
		return $this->number;
	}

	public function setNumberCrex(?int $numberCrex): void
	{
		$this->numberCrex = $numberCrex;
	}

	public function getIsCrex(): ?bool
	{
		return $this->isCrex;
	}

	public function setIsCrex(bool $isCrex): self
	{
		$this->isCrex = $isCrex;

		return $this;
	}

	public function getCreatedAt(): ?DateTime
	{
		return $this->createAt;
	}

	/**
	 * @param DateTime|null $createdAt
	 * @return $this
	 */
	public function setCreatedAt(?DateTime $createdAt): self
	{
		$this->createAt = $createdAt;

		return $this;
	}

	public function getDoneAt(): ?DateTime
	{
		return $this->doneAt;
	}

	/**
	 * @return $this
	 */
	public function setDoneAt(?DateTime $doneAt): self
	{
		$this->doneAt = $doneAt;

		return $this;
	}

	public function getStatus(): ?int
	{
		return $this->status;
	}

	/**
	 * @param $status
	 *
	 * @return $this
	 */
	public function setStatus($status): self
	{
		$this->status = $status;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getReader()
	{
		return $this->reader;
	}

	/**
	 * @param mixed $reader
	 */
	public function setReader($reader): void
	{
		$this->reader = $reader;
	}

	public function getValidatedAt(): ?DateTime
	{
		return $this->validatedAt;
	}

	/**
	 * @param DateTime|null $validatedAt
	 * @return $this
	 */
	public function setValidatedAt(?DateTime $validatedAt): self
	{
		$this->validatedAt = $validatedAt;

		return $this;
	}

	public function getType(): ?int
	{
		return $this->type;
	}

	/**
	 * @return $this
	 */
	public function setType(int $type): self
	{
		$this->type = $type;

		return $this;
	}

	public function getComment(): ?string
	{
		return $this->comment;
	}

	/**
	 * @param string|null $comment
	 * @return $this
	 */
	public function setComment(?string $comment): self
	{
		$this->comment = $comment;

		return $this;
	}

	/**
	 * @return Collection|DeviationSystemReviewAction[]
	 */
	public function getReviewActions(): Collection
	{
		return $this->reviewActions;
	}

	/**
	 * @param DeviationSystemReviewAction $reviewAction
	 * @return $this
	 */
	public function addReview(DeviationSystemReviewAction $reviewAction): self
	{
		if (!$this->reviewActions->contains($reviewAction)) {
			$this->reviewActions[] = $reviewAction;
			$reviewAction->setDeviationSystemReview($this);
		}

		return $this;
	}

	/**
	 * @param DeviationSystemReviewAction $reviewAction
	 * @return $this
	 */
	public function removeReview(DeviationSystemReviewAction $reviewAction): self
	{
		if ($this->reviewActions->removeElement($reviewAction)) {
			// set the owning side to null (unless already changed)
			if ($reviewAction->getDeviationSystemReview() === $this) {
				$reviewAction->setDeviationSystemReview(null);
			}
		}

		return $this;
	}

	public function getFieldsToBeIgnored(): array
	{
		return ['deviation'];
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
}
