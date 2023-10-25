<?php

namespace App\ESM\Entity\DropdownList;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="dl_institution_type")
 */
class InstitutionType
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
	 * @ORM\Column(type="string", length=5)
	 */
	private $code;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

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
	 * @return string|null
	 */
    public function getLabel(): ?string
    {
        return $this->label;
    }

	/**
	 * @param string $label
	 * @return $this
	 */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

	/**
	 * @return mixed
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @param mixed $code
	 */
	public function setCode($code): void
	{
		$this->code = $code;
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
}
