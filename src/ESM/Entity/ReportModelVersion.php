<?php

namespace App\ESM\Entity;

use App\ESM\Repository\ReportModelVersionRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=ReportModelVersionRepository::class)
 */
class ReportModelVersion
{
    public const STATUS_CREATE = 0;
    public const STATUS_PUBLISH = 1;
    public const STATUS_OBSOLETE = 2;
    public const STATUS = [
        self::STATUS_CREATE => 'entity.report_model_version.field.status.create',
        self::STATUS_PUBLISH => 'entity.report_model_version.field.status.publish',
        self::STATUS_OBSOLETE => 'entity.report_model_version.field.status.obsolete',
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $status;

    /**
     * @var User|null
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $createdBy;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var User|null
     *
     * @Gedmo\Blameable(on="change", field="status", value="1")
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="published_by", referencedColumnName="id")
     */
    private $publishedBy;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="change", field="status", value="1")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @var User|null
     *
     * @Gedmo\Blameable(on="change", field="status", value="2")
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="obsoleted_by", referencedColumnName="id")
     */
    private $obsoletedBy;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="change", field="status", value="2")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $obsoletedAt;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $numberVersion;

    /**
     * @ORM\ManyToOne(targetEntity=ReportModel::class, inversedBy="versions")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var ReportModel
     */
    private $reportModel;

    /**
     * @ORM\OneToMany(targetEntity="App\ESM\Entity\ReportConfig", mappedBy="modelVersion")
     */
    private $versionConfigs;

    /**
     * @ORM\OneToMany(targetEntity="App\ESM\Entity\ReportModelVersionBlock", mappedBy="version")
     */
    private $versionBlocks;

    /**
     * ReportModelVersion constructor.
     */
    public function __construct()
    {
        $this->versionBlocks = new ArrayCollection();
        $this->versionConfigs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->getNumberVersion();
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus($status): void
    {
        if (!array_key_exists($status, self::STATUS) && null !== $status) {
            throw new \Exception('status  '.$status.' not authorised !');
        }

        $this->status = $status;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return $this
     */
    public function setCreatedAt(?DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPublishedBy(): ?User
    {
        return $this->publishedBy;
    }

    public function setPublishedBy(?User $publishedBy): void
    {
        $this->publishedBy = $publishedBy;
    }

    public function getPublishedAt(): ?DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @return $this
     */
    public function setPublishedAt(?DateTime $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getObsoletedBy(): ?User
    {
        return $this->obsoletedBy;
    }

    public function setObsoletedBy(?User $obsoletedBy): void
    {
        $this->obsoletedBy = $obsoletedBy;
    }

    public function getObsoletedAt(): ?DateTime
    {
        return $this->obsoletedAt;
    }

    /**
     * @return $this
     */
    public function setObsoletedAt(?DateTime $obsoletedAt): self
    {
        $this->obsoletedAt = $obsoletedAt;

        return $this;
    }

    public function getNumberVersion(): int
    {
        return $this->numberVersion;
    }

    /**
     * @return $this
     */
    public function setNumberVersion(int $numberVersion): self
    {
        $this->numberVersion = $numberVersion;

        return $this;
    }

    public function getReportModel(): ReportModel
    {
        return $this->reportModel;
    }

    public function setReportModel(ReportModel $reportModel): void
    {
        $this->reportModel = $reportModel;
    }

    /**
     * @return ArrayCollection<int, ReportModelVersionBlock>
     */
    public function getVersionBlocks(): Collection
    {
        return $this->versionBlocks;
    }

    /**
     * @return $this
     */
    public function addVersionBlock(ReportModelVersionBlock $reportModelVersionBlock): self
    {
        if (!$this->versionBlocks->contains($reportModelVersionBlock)) {
            $this->versionBlocks[] = $reportModelVersionBlock;
			$reportModelVersionBlock->setVersion($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeVersionBlock(ReportModelVersionBlock $reportModelVersionBlock): self
    {
        if ($this->versionBlocks->contains($reportModelVersionBlock)) {
            $this->versionBlocks->removeElement($reportModelVersionBlock);
			$reportModelVersionBlock->setVersion(null);
        }

        return $this;
    }

    public function getLastBlockOrder(): int
    {
        $lastOrder = 1;
        foreach ($this->getVersionBlocks() as $versionBlock) {
            ++$lastOrder;
        }

        return $lastOrder;
    }

    /**
     * @return ArrayCollection<int, ReportConfigVersion>
     */
    public function getVersionConfigs(): Collection
    {
        return $this->versionConfigs;
    }

    /**
     * @return $this
     */
    public function addVersionConfig(ReportConfigVersion $reportConfigVersion): self
    {
        if (!$this->versionConfigs->contains($reportConfigVersion)) {
            $this->versionConfigs[] = $reportConfigVersion;
            $reportConfigVersion->setModelVersion($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeVersionConfig(ReportConfigVersion $reportConfigVersion): self
    {
        if ($this->versionConfigs->contains($reportConfigVersion)) {
            $this->versionConfigs->removeElement($reportConfigVersion);
            $reportConfigVersion->setModelVersion(null);
        }

        return $this;
    }
}
