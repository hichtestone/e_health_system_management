<?php

namespace App\ESM\Entity;

use App\ESM\Repository\ReportModelVersionBlockRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReportModelVersionBlockRepository::class)
 */
class ReportModelVersionBlock implements AuditrailableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\ReportModelVersion", inversedBy="versionBlocks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $version;

    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\ReportBlock", inversedBy="modelVersionBlocks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $block;

    /**
     * @ORM\Column(type="integer")
     */
    private $ordering;

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
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version): void
    {
        $this->version = $version;
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
}
