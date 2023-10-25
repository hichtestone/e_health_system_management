<?php

namespace App\ESM\Entity;

use App\ESM\Repository\ReportBlockRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReportBlockRepository::class)
 */
class ReportBlock
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
     * @ORM\Column(type="string", length=100, nullable=false)
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     *
     * @var bool
     */
    private $sys = false;

    /**
     * @ORM\OneToMany(targetEntity="App\ESM\Entity\ReportModelVersionBlock", mappedBy="block", cascade={"remove"})
     */
    private $modelVersionBlocks;

	/**
	 * @ORM\OneToMany(targetEntity="App\ESM\Entity\ReportConfigVersionBlock", mappedBy="block", cascade={"remove"})
	 */
	private $configVersionBlocks;

    /**
     * @ORM\OneToMany(targetEntity="App\ESM\Entity\ReportBlockParam", mappedBy="block", cascade={"remove"})
     */
    private $blockParams;

    /**
     * ReportBlock constructor.
     */
    public function __construct()
    {
        $this->modelVersionBlocks  = new ArrayCollection();
        $this->configVersionBlocks = new ArrayCollection();
        $this->blockParams         = new ArrayCollection();
    }

	/**
	 * @return int|null
	 */
    public function getId(): ?int
    {
        return $this->id;
    }

	/**
	 * @return array
	 */
    public function getFieldsToBeIgnored(): array
    {
        return [];
    }

	/**
	 * @return string
	 */
    public function __toString(): string
    {
        return $this->getName();
    }

	/**
	 * @return string|null
	 */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

	/**
	 * @return bool
	 */
    public function isSys(): bool
    {
        return $this->sys;
    }

	/**
	 * @param bool $isSys
	 */
    public function setSys(bool $isSys): void
    {
        $this->sys = $isSys;
    }

    /**
     * @return ArrayCollection<int, ReportModelVersionBlock>
     */
    public function getModelVersionBlocks(): Collection
    {
        return $this->modelVersionBlocks;
    }

    /**
     * @return $this
     */
    public function addModelVersionBlock(ReportModelVersionBlock $reportModelVersionBlock): self
    {
        if (!$this->modelVersionBlocks->contains($reportModelVersionBlock)) {
            $this->modelVersionBlocks[] = $reportModelVersionBlock;
			$reportModelVersionBlock->setBlock($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeModelVersionBlock(ReportModelVersionBlock $reportModelVersionBlock): self
    {
        if ($this->modelVersionBlocks->removeElement($reportModelVersionBlock)) {
            // set the owning side to null (unless already changed)
            if ($reportModelVersionBlock->getVersion() === $this) {
				$reportModelVersionBlock->setBlock(null);
            }
        }

        return $this;
    }

	/**
	 * @return ArrayCollection<int, ReportConfigVersionBlock>
	 */
	public function getConfigVersionBlocks(): Collection
	{
		return $this->configVersionBlocks;
	}

	/**
	 * @return $this
	 */
	public function addConfigVersionBlock(ReportConfigVersionBlock $reportConfigVersionBlock): self
	{
		if (!$this->configVersionBlocks->contains($reportConfigVersionBlock)) {
			$this->configVersionBlocks[] = $reportConfigVersionBlock;
			$reportConfigVersionBlock->setBlock($this);
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	public function removeConfigVersionBlock(ReportConfigVersionBlock $reportConfigVersionBlock): self
	{
		if ($this->modelVersionBlocks->removeElement($reportConfigVersionBlock)) {
			// set the owning side to null (unless already changed)
			if ($reportConfigVersionBlock->getVersion() === $this) {
				$reportConfigVersionBlock->setBlock(null);
			}
		}

		return $this;
	}

    /**
     * @return ArrayCollection<int, ReportBlockParam>
     */
    public function getBlockParams(): Collection
    {
        return $this->blockParams;
    }

    /**
     * @return $this
     */
    public function addBlockParam(ReportBlockParam $reportBlockParam): self
    {
        if (!$this->modelVersionBlocks->contains($reportBlockParam)) {
            $this->modelVersionBlocks[] = $reportBlockParam;
            $reportBlockParam->setBlock($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeBlockParam(ReportBlockParam $reportBlockParam): self
    {
        if ($this->modelVersionBlocks->contains($reportBlockParam)) {
            $this->modelVersionBlocks->removeElement($reportBlockParam);
            $reportBlockParam->setBlock(null);
        }

        return $this;
    }
}
