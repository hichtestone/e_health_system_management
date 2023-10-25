<?php

namespace App\ESM\Entity;

use App\ESM\Repository\ConditionVisitPatientRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConditionVisitPatientRepository::class)
 */
class ConditionVisitPatient
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=VisitPatient::class, inversedBy="conditionVisitPatients")
     */
    private $VisitPatient;

    /**
     * @ORM\ManyToOne(targetEntity=SchemaCondition::class, inversedBy="conditionVisitPatients")
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

    public function getVisitPatient(): ?VisitPatient
    {
        return $this->VisitPatient;
    }

    public function setVisitPatient(?VisitPatient $VisitPatient): self
    {
        $this->VisitPatient = $VisitPatient;

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
