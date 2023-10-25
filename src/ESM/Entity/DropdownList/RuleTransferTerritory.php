<?php

namespace App\ESM\Entity\DropdownList;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="dl_rule_transfer_territory")
 */
class RuleTransferTerritory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
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

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label): void
    {
        $this->label = $label;
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
