<?php

namespace App\ESM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class VariableList
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
     * @ORM\Column(type="string", length=55)
     *
     * @var string
     */
    private $label;

    /**
     * @ORM\OneToMany(targetEntity=VariableOption::class, mappedBy="list")
     *
     * @var ArrayCollection<int, VariableOption>
     */
    private $options;

    /**
     * @ORM\OneToMany(targetEntity=PatientVariable::class, mappedBy="variableList")
     *
     * @var ArrayCollection<int, PatientVariable>
     */
    private $patientVariables;

    public function __construct()
	{
		$this->options = new ArrayCollection();
	}

	public function __toString()
    {
        return $this->label;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

	/**
	 * @return Collection|VariableOption[]
	 */
	public function getOptions(): Collection
	{
		return $this->options;
	}

	/**
	 * @param VariableOption $option
	 * @return $this
	 */
	public function addProjectTrailTreatment(VariableOption $option): self
	{
		if (!$this->options->contains($option)) {
			$this->options[] = $option;
			$option->setList($this);
		}

		return $this;
	}

	/**
	 * @param VariableOption $option
	 * @return $this
	 */
	public function removeProjectTrailTreatment(VariableOption $option): self
	{
		if ($this->options->removeElement($option)) {
			$option->setList(null);
		}

		return $this;
	}
}
