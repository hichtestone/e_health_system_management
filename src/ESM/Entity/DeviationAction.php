<?php

namespace App\ESM\Entity;

use App\ESM\Repository\DeviationActionRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeviationActionRepository::class)
 */
class DeviationAction implements AuditrailableInterface
{
    public const STATUS_PROVIDE = 1;
    public const STATUS_EDITION = 2;
    public const STATUS_FINISH = 3;

    public const STATUS = [
        self::STATUS_PROVIDE => 'A prévoir',
        self::STATUS_EDITION => 'En cours',
        self::STATUS_FINISH => 'Effectuée',
    ];

    public const TYPE_MANAGER_PROJECT = 1;
    public const TYPE_MANAGER_CENTER = 2;

    public const TYPE_MANAGER = [
        self::TYPE_MANAGER_PROJECT => 'Projet',
        self::TYPE_MANAGER_CENTER => 'Centre',
    ];

    public const TYPE_ACTION_CORRECTIVE = 1;
    public const TYPE_ACTION_PREVENTIVE = 2;

    public const TYPE_ACTION = [
        self::TYPE_ACTION_CORRECTIVE => 'Corrective',
        self::TYPE_ACTION_PREVENTIVE => 'Préventive',
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
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
     * @ORM\Column(type="integer", nullable=false)
     *
     * @var int
     */
    private $typeManager;

    /**
     * @ORM\ManyToOne(targetEntity=Deviation::class, inversedBy="actions")
     * @ORM\JoinColumn(name="deviation_id", referencedColumnName="id")
     *
     * @var Deviation
     */
    private $deviation;

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
     * @ORM\ManyToOne(targetEntity=Interlocutor::class, inversedBy="deviationActions")
     * @ORM\JoinColumn(name="interlocutor_id", referencedColumnName="id", nullable=true)
     *
     * @var Interlocutor
     */
    private $interlocutor;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="deviationActions")
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->getDeviation()->getCode();
    }

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

    public function getTypeManager(): ?int
    {
        return $this->typeManager;
    }

    /**
     * @return $this
     */
    public function setTypeManager(int $typeManager): self
    {
        $this->typeManager = $typeManager;

        return $this;
    }

    public function getDeviation(): Deviation
    {
        return $this->deviation;
    }

    public function setDeviation(Deviation $deviation): void
    {
        $this->deviation = $deviation;
    }

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

    public function getIsDone(): bool
    {
        return $this->isDone;
    }

    public function setIsDone(bool $isDone): void
    {
        $this->isDone = $isDone;
    }

    public function getIsCrex(): bool
    {
        return $this->isCrex;
    }

    public function setIsCrex(bool $isCrex): void
    {
        $this->isCrex = $isCrex;
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
