<?php

namespace App\ESM\Entity\DropdownList\DeviationSystem;

use App\ESM\Entity\DeviationSystem;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="dl_process_system")
 */
class ProcessSystem
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
	 * @ORM\Column(type="string", length=2)
	 *
	 * @var string
	 */
	private $code;

	/**
	 * @ORM\Column(type="string", length=255)
	 *
	 * @var string
	 */
	private $label;

	/**
	 * @ORM\ManyToMany(targetEntity=DeviationSystem::class, mappedBy="process")
	 * @ORM\JoinTable(name="deviation_process_system")
	 *
	 * @var ArrayCollection<int, DeviationSystem>
	 */
	private $deviationsSystem;

	/**
	 * ProcessSystem constructor.
	 */
	public function __construct()
	{
		$this->deviationsSystem = new ArrayCollection();
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->getCode().' - '.$this->getLabel();
	}

	/**
	 * @return int|null
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getCode(): string
	{
		return $this->code;
	}

	/**
	 * @param string $code
	 */
	public function setCode(string $code): void
	{
		$this->code = $code;
	}

	/**
	 * @return string
	 */
	public function getLabel(): string
	{
		return $this->label;
	}

	/**
	 * @param string $label
	 */
	public function setLabel(string $label): void
	{
		$this->label = $label;
	}

	/**
	 * @return Collection
	 */
	public function getDeviationsSystem(): Collection
	{
		return $this->deviationsSystem;
	}

	/**
	 * @param DeviationSystem $deviationSystem
	 * @return $this
	 */
	public function addDeviationSystem(DeviationSystem $deviationSystem): self
	{
		if (!$this->deviationsSystem->contains($deviationSystem)) {
			$this->deviationsSystem[] = $deviationSystem;
		}

		return $this;
	}

	/**
	 * @param DeviationSystem $deviationSystem
	 * @return $this
	 */
	public function removeDeviationSystem(DeviationSystem $deviationSystem): self
	{
		if ($this->deviationsSystem->contains($deviationSystem)) {
			$this->deviationsSystem->removeElement($deviationSystem);
		}

		return $this;
	}
}
