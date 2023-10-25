<?php

namespace App\ESM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class VariableOption
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
     * @ORM\Column(type="string", length=55)
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
     * @ORM\ManyToOne(targetEntity=VariableList::class, inversedBy="options")
     *
     * @var VariableList
     */
    private $list;

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

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getList(): VariableList
    {
        return $this->list;
    }

    public function setList(VariableList $list): void
    {
        $this->list = $list;
    }
}
