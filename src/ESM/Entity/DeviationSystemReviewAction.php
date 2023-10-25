<?php

namespace App\ESM\Entity;

use App\ESM\Repository\DeviationSystemReviewActionRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeviationSystemReviewActionRepository::class)
 */
class DeviationSystemReviewAction
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="integer", nullable=false)
	 *
	 * @var int
	 */
	private $status;

	/**
	 * @ORM\Column(type="integer", nullable=false)
	 *
	 * @var int
	 */
	private $typeAction;

	/**
	 * @ORM\ManyToOne(targetEntity=DeviationSystemReview::class, inversedBy="reviewActions")
	 * @ORM\JoinColumn(name="deviation_system_review_id", referencedColumnName="id")
	 *
	 * @var DeviationSystemReview
	 */
	private $deviationSystemReview;

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
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="deviationSystemReviewActions")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
	 *
	 * @var User
	 */
	private $intervener;

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return int|null
	 */
	public function getStatus(): ?int
	{
		return $this->status;
	}

	/**
	 * @return $this
	 */
	public function setStatus(int $status): self
	{
		$this->status = $status;

		return $this;
	}

	/**
	 * @return int|null
	 */
	public function getTypeAction(): ?int
	{
		return $this->typeAction;
	}

	/**
	 * @return $this
	 */
	public function setTypeAction(int $typeAction): self
	{
		$this->typeAction = $typeAction;

		return $this;
	}

	/**
	 * @return DeviationSystemReview
	 */
	public function getDeviationSystemReview(): DeviationSystemReview
	{
		return $this->deviationSystemReview;
	}

	/**
	 * @param DeviationSystemReview $deviationSystemReview
	 */
	public function setDeviationSystemReview(DeviationSystemReview $deviationSystemReview): void
	{
		$this->deviationSystemReview = $deviationSystemReview;
	}

	/**
	 * @return string|null
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
	 * @return $this
	 */
	public function setDoneAt(?DateTime $doneAt): self
	{
		$this->doneAt = $doneAt;

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
}
