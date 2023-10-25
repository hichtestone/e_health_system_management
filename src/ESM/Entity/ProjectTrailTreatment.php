<?php

namespace App\ESM\Entity;

use App\ESM\Entity\DropdownList\TrailTreatment;
use App\ESM\Repository\ProjectTrailTreatmentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProjectTrailTreatmentRepository::class)
 */
class ProjectTrailTreatment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="projectTrailTreatments")
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity=TrailTreatment::class, inversedBy="projectsTrailtreatments")
     */
    private $trailTreatment;

    /**
     * @ORM\ManyToOne(targetEntity=Drug::class, inversedBy="projectTrailTreatments")
     */
    private $drug;

    /**
     * @ORM\ManyToOne(targetEntity=VersionDocumentTransverse::class, inversedBy="projectTrailTreatmentsBi")
     */
    private $versionBi;

    /**
     * @ORM\ManyToOne(targetEntity=VersionDocumentTransverse::class, inversedBy="projectTrailTreatmentsRcp")
     */
    private $versionRcp;

    /**
     * @return mixed
     */
    public function getTrailTreatment()
    {
        return $this->trailTreatment;
    }

    /**
     * @param mixed $trailTreatment
     */
    public function setTrailTreatment($trailTreatment): void
    {
        $this->trailTreatment = $trailTreatment;
    }

    /**
     * @return mixed
     */
    public function getDrug()
    {
        return $this->drug;
    }

    /**
     * @param mixed $drug
     */
    public function setDrug(?Drug $drug): void
    {
        $this->drug = $drug;
    }

    /**
     * @return mixed
     */
    public function getVersionBi()
    {
        return $this->versionBi;
    }

    /**
     * @param mixed $versionBi
     */
    public function setVersionBi(?VersionDocumentTransverse $versionBi): void
    {
        $this->versionBi = $versionBi;
    }

    /**
     * @return mixed
     */
    public function getVersionRcp()
    {
        return $this->versionRcp;
    }

    /**
     * @param mixed $versionRcp
     */
    public function setVersionRcp(?VersionDocumentTransverse $versionRcp): void
    {
        $this->versionRcp = $versionRcp;
    }

	/**
	 * @return int|null
	 */
    public function getId(): ?int
    {
        return $this->id;
    }

	/**
	 * @return Project|null
	 */
    public function getProject(): ?Project
    {
        return $this->project;
    }

	/**
	 * @param Project|null $project
	 * @return $this
	 */
    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }
}
