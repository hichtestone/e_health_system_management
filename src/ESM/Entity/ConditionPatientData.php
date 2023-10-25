<?php

namespace App\ESM\Entity;

use App\ESM\Repository\ConditionPatientDataRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConditionPatientDataRepository::class)
 */
class ConditionPatientData
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=PatientData::class, inversedBy="conditionPatientData")
     */
    private $PatientData;

    /**
     * @ORM\ManyToOne(targetEntity=SchemaCondition::class, inversedBy="conditionPatientData")
     */
    private $SchemaCondition;

    /**
     * @ORM\Column(type="datetime")
     */
    private $executedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPatientData(): ?PatientData
    {
        return $this->PatientData;
    }

    public function setPatientData(?PatientData $PatientData): self
    {
        $this->PatientData = $PatientData;

        return $this;
    }

    public function getSchemaCondition(): ?SchemaCondition
    {
        return $this->SchemaCondition;
    }

    public function setSchemaCondition(?SchemaCondition $SchemaCondition): self
    {
        $this->SchemaCondition = $SchemaCondition;

        return $this;
    }

    public function getExecutedAt(): ?\DateTimeInterface
    {
        return $this->executedAt;
    }

    public function setExecutedAt(\DateTimeInterface $executedAt): self
    {
        $this->executedAt = $executedAt;

        return $this;
    }
}
