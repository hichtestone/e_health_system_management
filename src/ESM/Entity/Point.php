<?php

namespace App\ESM\Entity;

use App\ESM\Repository\PointRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PointRepository::class)
 */
class Point
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $x;

    /**
     * @ORM\Column(type="integer")
     */
    private $y;

    /**
     * @ORM\ManyToOne(targetEntity=CourbeSetting::class, inversedBy="points")
     */
    private $courbeSetting;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getX(): ?int
    {
        return $this->x;
    }

    public function setX(int $x): self
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): ?int
    {
        return $this->y;
    }

    public function setY(int $y): self
    {
        $this->y = $y;

        return $this;
    }

    public function getCourbeSetting(): ?CourbeSetting
    {
        return $this->courbeSetting;
    }

    public function setCourbeSetting(?CourbeSetting $courbeSetting): self
    {
        $this->courbeSetting = $courbeSetting;

        return $this;
    }

    public function clearCourbeSettings(): self
    {
        $this->courbeSetting = null;

        return $this;
    }

    public function __toString()
    {
        return '';
    }

    public function getFieldsToBeIgnored(): array
    {
        return [];
    }
}
