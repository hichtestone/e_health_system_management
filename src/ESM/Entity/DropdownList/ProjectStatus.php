<?php

namespace App\ESM\Entity\DropdownList;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="dl_project_status")
 */
class ProjectStatus
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

	/**
	 * @ORM\Column(type="string", length=25)
	 */
	private $code;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAuto = false;

	/**
	 * @return mixed
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


    public function getDeleteAt(): ?DateTime
    {
        return $this->deleteAt;
    }

	/**
	 * @param DateTime|null $deleteAt
	 * @return $this
	 */
    public function setDeleteAt(?DateTime $deleteAt): self
    {
        $this->deleteAt = $deleteAt;

        return $this;
    }

	/**
	 * @return bool
	 */
    public function isAuto(): bool
    {
        return $this->isAuto;
    }

	/**
	 * @param bool $isAuto
	 */
    public function setIsAuto(bool $isAuto): void
    {
        $this->isAuto = $isAuto;
    }
}
