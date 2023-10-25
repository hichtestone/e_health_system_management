<?php

namespace App\ESM\Entity;

use App\ESM\Repository\VisitPatientStatusRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=VisitPatientStatusRepository::class)
 */
class VisitPatientStatus
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
	 * @Groups("visit-patient")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=55)
     *
     * @var string
	 * @Groups("visit-patient")
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=5)
     *
     * @var string
     */
    private $code;

    /**
     * @ORM\Column(type="smallint")
     *
     * @var int
     */
    private $position;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->getLabel();
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}
