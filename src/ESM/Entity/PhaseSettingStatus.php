<?php

namespace App\ESM\Entity;

use App\ESM\Repository\PhaseSettingStatusRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PhaseSettingStatusRepository::class)
 */
class PhaseSettingStatus implements AuditrailableInterface
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $id;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $label;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $code;

	/**
	 * @ORM\OneToMany(targetEntity=PhaseSetting::class, mappedBy="phaseSettingStatus")
	 *
	 * @var ArrayCollection<int, PhaseSetting>
	 */
	private $phaseSettings;

	public function __construct()
	{
		$this->phaseSettings = new ArrayCollection();
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->label;
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
	 * @return ArrayCollection
	 */
	public function getPhaseSettings(): ArrayCollection
	{
		return $this->phaseSettings;
	}

	public function getFieldsToBeIgnored(): array
	{
		return [];
	}
}
