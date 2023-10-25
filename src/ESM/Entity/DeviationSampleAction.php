<?php

namespace App\ESM\Entity;

use App\ESM\Repository\DeviationSampleActionRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=DeviationSampleActionRepository::class)
 */
class DeviationSampleAction implements AuditrailableInterface
{
	public const TYPE_MANAGER_INTERVENANT = 1;
	public const TYPE_MANAGER_INTERLOCUTOR = 2;

	public const TYPE_MANAGER = [
		self::TYPE_MANAGER_INTERVENANT => 'Intervenant',
		self::TYPE_MANAGER_INTERLOCUTOR => 'Interlocuteur',
	];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
	 *
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int|null
     */
    private $status;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int|null
     */
    private $typeAction;

    /**
     * @ORM\Column(type="integer", nullable=false)
     *
     * @var int
     */
    private $typeManager;

    /**
     * @ORM\ManyToOne(targetEntity=DeviationSample::class, inversedBy="deviationSampleActions")
     * @ORM\JoinColumn(name="deviation_sample_id", referencedColumnName="id")
     *
     * @var DeviationSample
     */
    private $deviationSample;

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
     * @ORM\ManyToOne(targetEntity=Interlocutor::class, inversedBy="deviationActions")
     * @ORM\JoinColumn(name="interlocutor_id", referencedColumnName="id", nullable=true)
     *
     * @var Interlocutor
     */
    private $interlocutor;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="deviationSampleActions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     *
     * @var User
     */
    private $user;

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
        return $this->getDeviationSample()->getCode();
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
	 * @throws Exception
	 */
	public function setStatus(?int $status): void
	{
		if (!array_key_exists($status, DeviationAction::STATUS)) {
			throw new Exception('the status  ' . $status . ' is not authorize !');
		}

		$this->status = $status;
	}


	/**
	 * @return int|null
	 */
    public function getTypeAction(): ?int
    {
        return $this->typeAction;
    }

	/**
	 * @param int|null $typeAction
	 * @throws Exception
	 */
	public function setTypeAction(?int $typeAction): void
	{
		if (!array_key_exists($typeAction, DeviationAction::TYPE_ACTION)) {
			throw new Exception('the type_manager  ' . $typeAction . ' is not authorize !');
		}

		$this->typeAction = $typeAction;
	}

	/**
	 * @return int|null
	 */
    public function getTypeManager(): ?int
    {
        return $this->typeManager;
    }

	/**
	 * @param int|null $typeManager
	 * @throws Exception
	 */
	public function setTypeManager(?int $typeManager): void
	{
		if (!array_key_exists($typeManager, DeviationSampleAction::TYPE_MANAGER)) {
			throw new Exception('the type_manager  ' . $typeManager . ' is not authorize !');
		}

		$this->typeManager = $typeManager;
	}

	/**
	 * @return DeviationSample
	 */
    public function getDeviationSample(): DeviationSample
    {
        return $this->deviationSample;
    }

	/**
	 * @param DeviationSample $deviationSample
	 */
    public function setDeviationSample(DeviationSample $deviationSample): void
    {
        $this->deviationSample = $deviationSample;
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
	 * @return $this
	 */
    public function setDescription(?string $description): self
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
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
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
	 * @return $this
	 */
    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

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

    public function getFieldsToBeIgnored(): array
    {
        return [];
    }
}
