<?php

namespace App\ESM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class VariableType
{
	public const TYPE_NUMERIC_LABEL = 'numeric';
	public const TYPE_DATE_LABEL 	= 'date';
	public const TYPE_STRING_LABEL 	= 'string';
	public const TYPE_LIST_LABEL 	= 'list';

	/**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=55)
     *
     * @var string
     */
    private $label;

    public function __toString(): string
    {
        return $this->getLabel();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }
}
