<?php

namespace App\ESM\Entity\DropdownList;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="dl_crf_type")
 */
class CrfType
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
     * @ORM\Column(type="string", length=20)
     *
     * @var string
     */
    private $label;

	/**
	 * @ORM\Column(type="string", length=20)
	 *
	 * @var string
	 */
	private $code;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $deletedAt;

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

	/**
	 * @param string $label
	 */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

	/**
	 * @return string
	 */
	public function getCode(): string
	{
		return $this->code;
	}

	/**
	 * @param string $code
	 */
	public function setCode(string $code): void
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
