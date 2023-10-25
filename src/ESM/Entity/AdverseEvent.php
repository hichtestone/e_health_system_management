<?php

namespace App\ESM\Entity;

use App\ESM\Repository\AdverseEventRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AdverseEventRepository::class)
 */
class AdverseEvent implements AuditrailableInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $serious = false;

    /**
     * @ORM\Column(type="string", length=20)
     *
     * @var string
     */
    private $number;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="adverseEvents")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Patient
     */
    private $patient;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    private $occuredAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $deletedAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     */
    private $sourceId;

	/**
	 * @return int|null
	 */
    public function getId(): ?int
    {
        return $this->id;
    }

	/**
	 * @return string[]
	 */
    public function getFieldsToBeIgnored(): array
    {
        return ['patient'/*'source_id'*/];
    }

	/**
	 * @return bool
	 */
    public function isSerious(): bool
    {
        return $this->serious;
    }

	/**
	 * @param bool $serious
	 */
    public function setSerious(bool $serious): void
    {
        $this->serious = $serious;
    }

	/**
	 * @return string
	 */
    public function getNumber(): string
    {
        return $this->number;
    }

	/**
	 * @param string $number
	 */
    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

	/**
	 * @return Patient
	 */
    public function getPatient(): Patient
    {
        return $this->patient;
    }

	/**
	 * @param Patient $patient
	 */
    public function setPatient(Patient $patient): void
    {
        $this->patient = $patient;
    }

	/**
	 * @return DateTime
	 */
    public function getOccuredAt(): DateTime
    {
        return $this->occuredAt;
    }

	/**
	 * @param DateTime $occuredAt
	 */
    public function setOccuredAt(DateTime $occuredAt): void
    {
        $this->occuredAt = $occuredAt;
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
	 * @return int
	 */
    public function getSourceId(): int
    {
        return $this->sourceId;
    }

	/**
	 * @param int $sourceId
	 */
    public function setSourceId(int $sourceId): void
    {
        $this->sourceId = $sourceId;
    }

	/**
	 * @return bool|null
	 */
    public function getSerious(): ?bool
    {
        return $this->serious;
    }
}
