<?php

namespace App\ESM\Entity;

use App\ESM\Repository\CourbeSettingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CourbeSettingRepository::class)
 */
class CourbeSetting
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datestrat;

    /**
     * @return mixed
     */
    public function getDatestrat()
    {
        return $this->datestrat;
    }

    /**
     * @param mixed $datestrat
     */
    public function setDatestrat($datestrat): void
    {
        $this->datestrat = $datestrat;
    }

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $unit;

    /**
     * @ORM\OneToOne(targetEntity=Project::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Project
     */
    private $project;

    /**
     * @ORM\OneToMany(targetEntity=Point::class, mappedBy="courbeSetting",cascade={"persist"})
     */
    private $points;

    /**
     * @return Collection|Point[]
     */
    public function getPoints(): Collection
    {
        return $this->points;
    }

    public function setPoints(ArrayCollection $points): void
    {
        $this->points = $points;
    }

    public function __construct()
    {
        $this->points = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->datestrat;
    }

    public function setStartat(?\DateTimeInterface $datestrat): self
    {
        $this->datestrat = $datestrat;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function removePoint(Point $point): void
    {
        $this->points->removeElement($point);
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function __toString()
    {
        return $this->unit;
    }

    public function getFieldsToBeIgnored(): array
    {
        return [];
    }

    /**
     * @return Collection|Point[]
     */
    public function addPoint(Point $point): void
    {
        $this->points->add($point);
    }

    public function removepoints(Point $points): self
    {
        if ($this->points->removeElement($points)) {
            // set the owning side to null (unless already changed)
            if ($points->getCourbeSetting() === $this) {
                $points->setCourbeSetting(null);
            }
        }

        return $this;
    }
}
