<?php

namespace App\ESM\Entity\DropdownList\DeviationSample;

use App\ESM\Entity\DeviationSample;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="dl_process_involved")
 */
class ProcessInvolved
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
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $label;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $deletedAt;

	/**
	 * @ORM\ManyToMany(targetEntity=DeviationSample::class, mappedBy="processInvolves")
	 * @ORM\JoinTable(name="deviation_sample_process_involved")
	 *
	 * @var ArrayCollection<int, DeviationSample>
	 */
	private $deviationSamples;

	public function __construct()
	{
		$this->deviationSamples = new ArrayCollection();
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

    public function setLabel(string $label): void
    {
        $this->label = $label;
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
	 * @return Collection
	 */
	public function getDeviationSamples(): Collection
	{
		return $this->deviationSamples;
	}

	/**
	 * @param DeviationSample $deviationSample
	 * @return $this
	 */
	public function addDeviationSample(DeviationSample $deviationSample): self
	{
		if (!$this->deviationSamples->contains($deviationSample)) {
			$this->deviationSamples[] = $deviationSample;
		}

		return $this;
	}

	/**
	 * @param DeviationSample $deviationSample
	 * @return $this
	 */
	public function removeDeviationSample(DeviationSample $deviationSample): self
	{
		if ($this->deviationSamples->contains($deviationSample)) {
			$this->deviationSamples->removeElement($deviationSample);
		}

		return $this;
	}
}
