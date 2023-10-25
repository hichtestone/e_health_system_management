<?php

namespace App\ESM\Entity;

use App\ESM\Repository\ReportBlockParamRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReportBlockParamRepository::class)
 */
class ReportBlockParam
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     *
     * @var string|null
     */
    private $label;

    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\ReportBlock", inversedBy="blockParams")
     * @ORM\JoinColumn(nullable=false)
     */
    private $block;

    /**
     * @ORM\Column(type="integer")
     */
    private $ordering;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $param;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param string|null $label
     * @return $this
     */
    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return ReportBlock
     */
    public function getBlock(): ?ReportBlock
    {
        return $this->block;
    }

    public function setBlock(?ReportBlock $block): void
    {
        $this->block = $block;
    }

    /**
     * @return mixed
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
     * @param mixed $ordering
     */
    public function setOrdering($ordering): void
    {
        $this->ordering = $ordering;
    }

    public function getParam(): ?string
    {
        return $this->param;
    }

    public function setParam(?string $param): void
    {
        $this->param = $param;
    }
}
