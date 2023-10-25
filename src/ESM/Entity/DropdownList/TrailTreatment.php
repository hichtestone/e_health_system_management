<?php

namespace App\ESM\Entity\DropdownList;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\ESM\Entity\Drug;
use App\ESM\Entity\ProjectTrailTreatment;

/**
 * @ORM\Entity
 * @ORM\Table(name="dl_trail_treatment")
 */
class TrailTreatment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=55)
     */
    private $label;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deleteAt;

	/**
	 * @ORM\OneToMany(targetEntity=Drug::class, mappedBy="trailTreatment")
	 */
	private $drugs;

	/**
	 * @ORM\OneToMany(targetEntity=ProjectTrailTreatment::class, mappedBy="trailTreatment")
	 */
	private $projectsTrailtreatments;

    public function __toString()
    {
        return $this->label;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getDeleteAt(): ?DateTime
    {
        return $this->deleteAt;
    }

    public function setDeleteAt(?DateTime $deleteAt): self
    {
        $this->deleteAt = $deleteAt;

        return $this;
    }

	/**
	 * @return Collection|Drug[]
	 */
	public function getDrugs(): Collection
	{
		return $this->drugs;
	}

	/**
	 * @return $this
	 */
	public function addDrug(Drug $drug): self
	{
		if (!$this->drugs->contains($drug)) {
			$this->drugs[] = $drug;
			$drug->setTrailTreatment($this);
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	public function removeDrug(Drug $drug): self
	{
		if ($this->drugs->removeElement($drug)) {
			// set the owning side to null (unless already changed)
			if ($drug->getTrailTreatment() === $this) {
				$drug->setTrailTreatment(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection|ProjectTrailTreatment[]
	 */
	public function getProjectsTrailTreatment(): Collection
	{
		return $this->projectsTrailtreatments;
	}

	/**
	 * @param ProjectTrailTreatment $projectTrailTreatment
	 * @return $this
	 */
	public function addProjectTrailTreatment(ProjectTrailTreatment $projectTrailTreatment): self
	{
		if (!$this->projectsTrailtreatments->contains($projectTrailTreatment)) {
			$this->projectsTrailtreatments[] = $projectTrailTreatment;
		}

		return $this;
	}

	/**
	 * @param ProjectTrailTreatment $projectTrailTreatment
	 * @return $this
	 */
	public function removeProjectTrailTreatment(ProjectTrailTreatment $projectTrailTreatment): self
	{
		$this->projectsTrailtreatments->removeElement($projectTrailTreatment);

		return $this;
	}
}
