<?php

namespace App\ESM\Entity;

use App\ESM\Entity\DropdownList\ProjectDatabaseFreezeReason;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ProjectDatabaseFreeze implements AuditrailableInterface
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
     * @ORM\ManyToOne(targetEntity=Date::class, inversedBy="databaseFreezes")
     */
    private $projectDate;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $frozenAt;

    /**
     * @ORM\ManyToOne(targetEntity=ProjectDatabaseFreezeReason::class)
     */
    private $reason;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $otherReason;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $reportDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->reason;
    }

    public function getFrozenAt()
    {
        return $this->frozenAt;
    }

    public function setFrozenAt($frozenAt): void
    {
        $this->frozenAt = $frozenAt;
    }

    public function getReason()
    {
        return $this->reason;
    }

    public function setReason($reason): void
    {
        $this->reason = $reason;
    }

    public function getOtherReason()
    {
        return $this->otherReason;
    }

    public function setOtherReason($otherReason): void
    {
        $this->otherReason = $otherReason;
    }

    public function getReportDate()
    {
        return $this->reportDate;
    }

    public function setReportDate($reportDate): void
    {
        $this->reportDate = $reportDate;
    }

    public function getProjectDate()
    {
        return $this->projectDate;
    }

    public function setProjectDate(Date $projectDate): void
    {
        $this->projectDate = $projectDate;
    }

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function setDeletedAt($deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    public function getFieldsToBeIgnored(): array
    {
        return [];
    }
}
