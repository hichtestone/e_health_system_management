<?php

namespace App\ESM\Entity\DropdownList;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="dl_formality_rule")
 */
class FormalityRule
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
}
