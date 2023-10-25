<?php

namespace App\ESM\Entity;

use App\ESM\Repository\DeviationSampleCorrectionRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=DeviationSampleCorrectionRepository::class)
 */
class DeviationSampleCorrection implements AuditrailableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
	 *
	 * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $applicationPlannedAt;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    private $realizedAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
	 *
	 * @var int
     */
    private $efficiencyMeasure;

    /**
     * @ORM\Column(type="text", nullable=true)
	 *
	 * @var string
     */
    private $notEfficiencyMeasureReason;

    /**
     * @ORM\ManyToOne(targetEntity=DeviationSample::class, inversedBy="deviationSampleCorrections")
	 * @ORM\JoinColumn(name="deviation_sample_id", referencedColumnName="id")
	 *
	 * @var DeviationSample
	 */
	private $deviationSample;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $deletedAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null
     */
    private $comment;

    public function getId(): ?int
    {
        return $this->id;
    }

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return (string) $this->getDeviationSample()->getCode();
	}

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

	/**
	 * @return DateTime|null
	 */
    public function getApplicationPlannedAt(): ?DateTime
    {
        return $this->applicationPlannedAt;
    }

	/**
	 * @param DateTime|null $applicationPlannedAt
	 */
    public function setApplicationPlannedAt(?DateTime $applicationPlannedAt): void
    {
        $this->applicationPlannedAt = $applicationPlannedAt;
    }

	/**
	 * @return DateTime|null
	 */
    public function getRealizedAt(): ?DateTime
    {
        return $this->realizedAt;
    }

	/**
	 * @param DateTime $realizedAt
	 */
    public function setRealizedAt(DateTime $realizedAt): void
    {
        $this->realizedAt = $realizedAt;
    }

	/**
	 * @return int
	 */
	public function getEfficiencyMeasure(): ?int
	{
		return $this->efficiencyMeasure;
	}

	/**
	 * @param int|null $efficiencyMeasure
	 *
	 * @throws Exception
	 */
	public function setEfficiencyMeasure(?int $efficiencyMeasure): void
	{
		if (!array_key_exists($efficiencyMeasure, Deviation::EFFICIENCY_MEASURE)) {
			throw new Exception('the efficiency measure  ' . $efficiencyMeasure . ' is not authorize !');
		}

		$this->efficiencyMeasure = $efficiencyMeasure;
	}

	/**
	 * @return string|null
	 */
    public function getNotEfficiencyMeasureReason(): ?string
    {
        return $this->notEfficiencyMeasureReason;
    }

	/**
	 * @param string|null $notEfficiencyMeasureReason
	 */
    public function setNotEfficiencyMeasureReason(?string $notEfficiencyMeasureReason): void
    {
        $this->notEfficiencyMeasureReason = $notEfficiencyMeasureReason;
    }

	/**
	 * @return DeviationSample
	 */
    public function getDeviationSample(): DeviationSample
    {
        return $this->deviationSample;
    }

	/**
	 * @param DeviationSample $deviation
	 */
    public function setDeviationSample(DeviationSample $deviation): void
    {
        $this->deviationSample = $deviation;
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

	public function getFieldsToBeIgnored(): array
	{
		return [];
	}
}
