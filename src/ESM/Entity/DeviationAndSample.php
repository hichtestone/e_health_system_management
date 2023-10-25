<?php

namespace App\ESM\Entity;

use App\ESM\Repository\DeviationAndSampleRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeviationAndSampleRepository::class)
 *
 */
class DeviationAndSample implements AuditrailableInterface
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
     * @ORM\ManyToOne(targetEntity=Deviation::class, inversedBy="deviationAndSamples")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Deviation
     */
    private $deviation;

    /**
     * @ORM\ManyToOne(targetEntity=DeviationSample::class, inversedBy="deviationAndSamples")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var DeviationSample
     */
    private $deviationSample;

	/**
	 * @return int
	 */
    public function getId(): int
    {
        return $this->id;
    }

    public function getFieldsToBeIgnored(): array
    {
        return ['deviation', 'deviationSample'];
    }

    public function __toString()
    {
        return $this->getDeviation()->getCode() ?? '';
    }

	/**
	 * @return Deviation|null
	 */
    public function getDeviation(): ?Deviation
    {
        return $this->deviation;
    }

	/**
	 * @param Deviation|null $deviation
	 */
    public function setDeviation(?Deviation $deviation): void
    {
        $this->deviation = $deviation;
    }

	/**
	 * @return DeviationSample|null
	 */
    public function getDeviationSample(): ?DeviationSample
    {
        return $this->deviationSample;
    }

	/**
	 * @param DeviationSample|null $deviationSample
	 */
    public function setDeviationSample(?DeviationSample $deviationSample): void
    {
        $this->deviationSample = $deviationSample;
    }

}
