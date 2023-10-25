<?php

namespace App\ESM\Entity\DropdownList;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="dl_membership_group")
 */
class MembershipGroup
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
	 * @ORM\Column(type="integer")
	 *
	 * @var int
	 */
	private $position;

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

	/**
	 * @return int
	 */
	public function getPosition(): int
	{
		return $this->position;
	}

	/**
	 * @param int $position
	 */
	public function setPosition(int $position): void
	{
		$this->position = $position;
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
