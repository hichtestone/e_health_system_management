<?php

namespace App\ESM\Entity;

use App\ESM\Entity\DropdownList\FormalityRule;
use App\ESM\Entity\DropdownList\RuleTransferTerritory;
use App\ESM\Repository\RuleRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RuleRepository::class)
 */
class Rule implements AuditrailableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=FormalityRule::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $formality;

    /**
     * @ORM\Column(type="boolean")
     */
    private $conformity = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $studyTransfer = false;

    /**
     * @ORM\ManyToOne(targetEntity=RuleTransferTerritory::class)
     * @ORM\JoinColumn(nullable=true)
     *
     * @var RuleTransferTerritory|null
     */
    private $studyTransferTerritory;

    /**
     * @ORM\ManyToOne(targetEntity=RuleTransferTerritory::class)
     * @ORM\JoinColumn(nullable=true)
     *
     * @var RuleTransferTerritory|null
     */
    private $outTransferTerritory;

    /**
     * @ORM\Column(type="boolean")
     */
    private $outStudyTransfer = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $post = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $partner;

    /**
     * @ORM\Column(type="boolean")
     */
    private $mapping = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reference;

    /**
     * @ORM\Column(type="boolean")
     */
    private $validateMapping = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $dataProtection = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $dataAccess = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $eTmf;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Project
     */
    private $project;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): ?string
    {
        return $this->getProject()->getName();
    }

    public function getFieldsToBeIgnored(): array
    {
        return [];
    }

    public function getFormality(): ?FormalityRule
    {
        return $this->formality;
    }

    public function setFormality(?FormalityRule $formality): void
    {
        $this->formality = $formality;
    }

    public function getConformity(): ?bool
    {
        return $this->conformity;
    }

    public function setConformity(bool $conformity): self
    {
        $this->conformity = $conformity;

        return $this;
    }

    public function getStudyTransfer(): ?bool
    {
        return $this->studyTransfer;
    }

    public function setStudyTransfer(bool $studyTransfer): self
    {
        $this->studyTransfer = $studyTransfer;

        return $this;
    }

    public function getOutStudyTransfer(): ?bool
    {
        return $this->outStudyTransfer;
    }

    public function setOutStudyTransfer(bool $outStudyTransfer): self
    {
        $this->outStudyTransfer = $outStudyTransfer;

        return $this;
    }

    public function getPost(): ?bool
    {
        return $this->post;
    }

    public function setPost(?bool $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getPartner(): ?string
    {
        return $this->partner;
    }

    public function setPartner(?string $partner): self
    {
        $this->partner = $partner;

        return $this;
    }

    public function getMapping(): ?bool
    {
        return $this->mapping;
    }

    public function setMapping(bool $mapping): self
    {
        $this->mapping = $mapping;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getValidateMapping(): ?bool
    {
        return $this->validateMapping;
    }

    public function setValidateMapping(bool $validateMapping): self
    {
        $this->validateMapping = $validateMapping;

        return $this;
    }

    public function getDataProtection(): ?bool
    {
        return $this->dataProtection;
    }

    public function setDataProtection(bool $dataProtection): self
    {
        $this->dataProtection = $dataProtection;

        return $this;
    }

    public function getDataAccess(): ?bool
    {
        return $this->dataAccess;
    }

    public function setDataAccess(bool $dataAccess): self
    {
        $this->dataAccess = $dataAccess;

        return $this;
    }

    public function getETmf(): ?string
    {
        return $this->eTmf;
    }

    public function setETmf(?string $eTmf): self
    {
        $this->eTmf = $eTmf;

        return $this;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    public function getStudyTransferTerritory(): ?RuleTransferTerritory
    {
        return $this->studyTransferTerritory;
    }

    public function setStudyTransferTerritory(?RuleTransferTerritory $studyTransferTerritory): void
    {
        $this->studyTransferTerritory = $studyTransferTerritory;
    }

    public function getOutTransferTerritory(): ?RuleTransferTerritory
    {
        return $this->outTransferTerritory;
    }

    public function setOutTransferTerritory(?RuleTransferTerritory $outTransferTerritory): void
    {
        $this->outTransferTerritory = $outTransferTerritory;
    }
}
