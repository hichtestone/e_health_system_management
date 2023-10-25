<?php

namespace App\ESM\Entity\DropdownList;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="dl_exam_status")
 */
class ExamStatus
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
	 *
	 * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
	 *
	 * @var string
     */
    private $label;

	/**
	 * @ORM\Column(type="string", length=55)
	 *
	 * @var string
	 */
	private $code;

	/**
     * @ORM\Column(type="datetime", nullable=true)
	 *
	 * @var DateTime|null
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

	public function getCode(): ?string
	{
		return $this->code;
	}

	public function setCode(string $code): self
	{
		$this->code = $code;

		return $this;
	}

    public function getDeletedAt(): ?DateTime
    {
        return $this->deleteAt;
    }

    public function setDeletedAt(?DateTime $deleteAt): self
    {
        $this->deleteAt = $deleteAt;

        return $this;
    }
}
