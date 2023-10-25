<?php

namespace App\ESM\Entity;

use App\ESM\Repository\ReportConfigVersionBlockRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReportConfigVersionBlockRepository::class)
 */
class ReportConfigVersionBlock implements AuditrailableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\ReportConfigVersion", inversedBy="versionBlocks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $configVersion;

    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\ReportBlock", inversedBy="configVersionBlocks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $block;

    /**
     * @ORM\Column(type="integer")
     */
    private $ordering;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     *
     * @var bool
     */
    private $active = false;


    public function getFieldsToBeIgnored(): array
    {
        // TODO: Implement getFieldsToBeIgnored() method.
    }

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

	/**
	 * @return bool
	 */
    public function isActive(): bool
    {
        return $this->active;
    }

	/**
	 * @param bool $active
	 */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return mixed
     */
    public function getConfigVersion(): ReportConfigVersion
    {
        return $this->configVersion;
    }

	/**
	 * @param ReportConfigVersion|null $configVersion
	 */
	public function setConfigVersion(?ReportConfigVersion $configVersion): void
	{
		$this->configVersion = $configVersion;
	}

	/**
	 * @return User|null
	 */
    public function getConfigVersionConfiguredBy()
	{
		return $this->getConfigVersion()->getConfiguredBy();
	}

	/**
	 * @return DateTime|null
	 */
	public function getConfigVersionStartedAt()
	{
		return $this->getConfigVersion()->getStartedAt();
	}

	/**
	 * @return DateTime|null
	 */
	public function getConfigVersionEndedAt()
	{
		return $this->getConfigVersion()->getEndedAt();
	}

	/**
	 * @return int
	 */
	public function getModelVersion()
	{
		return $this->getConfigVersion()->getConfig()->getModelVersion()->getNumberVersion();
	}

    /**
     * @param mixed $configVersion
     */
    public function setVersion($configVersion): void
    {
        $this->configVersion = $configVersion;
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

	/**
	 * @return mixed
	 */
	public function getBlock()
	{
		return $this->block;
	}

	/**
	 * @param mixed $block
	 */
	public function setBlock($block): void
	{
		$this->block = $block;
	}
}
