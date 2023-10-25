<?php

namespace App\ESM\Entity;

use App\ESM\Repository\DeviationCorrectionRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=DeviationCorrectionRepository::class)
 */
class DeviationCorrection implements AuditrailableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime
     */
    private $applicationPlannedAt;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    private $realizedAt;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $efficiencyMeasure;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $notEfficiencyMeasureReason;

    /**
     * @ORM\ManyToOne(targetEntity=Deviation::class, inversedBy="deviationCorrections")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Deviation
     */
    private $deviation;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="deviationCorrections")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Project
     */
    private $project;

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

    public function getFieldsToBeIgnored(): array
    {
        return [];
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

    public function getApplicationPlannedAt(): ?DateTime
    {
        return $this->applicationPlannedAt;
    }

    public function setApplicationPlannedAt(?DateTime $applicationPlannedAt): void
    {
        $this->applicationPlannedAt = $applicationPlannedAt;
    }

    public function getRealizedAt(): ?DateTime
    {
        return $this->realizedAt;
    }

    public function setRealizedAt(DateTime $realizedAt): void
    {
        $this->realizedAt = $realizedAt;
    }

    /**
     * @return string
     */
    public function getEfficiencyMeasure(): ?string
    {
        return $this->efficiencyMeasure;
    }

    /**
     * @throws Exception
     */
    public function setEfficiencyMeasure(?string $efficiencyMeasure): void
    {
        if (!array_key_exists($efficiencyMeasure, Deviation::EFFICIENCY_MEASURE)) {
            throw new Exception('the efficiency measure  '.$efficiencyMeasure.' is not authorize !');
        }

        $this->efficiencyMeasure = $efficiencyMeasure;
    }

    /**
     * @return mixed
     */
    public function getNotEfficiencyMeasureReason()
    {
        return $this->notEfficiencyMeasureReason;
    }

    /**
     * @param $notEfficiencyMeasureReason
     */
    public function setNotEfficiencyMeasureReason($notEfficiencyMeasureReason): void
    {
        $this->notEfficiencyMeasureReason = $notEfficiencyMeasureReason;
    }

    public function getDeviation(): Deviation
    {
        return $this->deviation;
    }

    public function setDeviation(Deviation $deviation): void
    {
        $this->deviation = $deviation;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
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

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getDeviation()->getCode();
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
}
