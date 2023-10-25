<?php

namespace App\ESM\Entity;

use App\ESM\Entity\DropdownList\CallProject;
use App\ESM\Entity\DropdownList\Devise;
use App\ESM\Entity\DropdownList\Funder;
use App\ESM\Repository\FundingRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=FundingRepository::class)
 */
class Funding implements AuditrailableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $publicFunding;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThanOrEqual(
     *     propertyPath="demandedAt",
     *     message="La date d'obtention du financement doit être supérieure ou égale de la date de demande AAP"
     * )
     */
    private $obtainedAt;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $demandedAt;

    /**
     * @ORM\ManyToOne(targetEntity=CallProject::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $callProject;

    /**
     * @ORM\ManyToOne(targetEntity=Devise::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $devise;

    /**
     * @ORM\ManyToOne(targetEntity=Funder::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $funder;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Project
     */
    private $project;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $deletedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFieldsToBeIgnored(): array
    {
        return ['project'];
    }

    public function __toString()
    {
        return (string) $this->funder->getLabel();
    }

    public function getPublicFunding(): ?bool
    {
        return $this->publicFunding;
    }

    public function setPublicFunding(?bool $publicFunding): self
    {
        $this->publicFunding = $publicFunding;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getObtainedAt(): ?DateTime
    {
        return $this->obtainedAt;
    }

    public function setObtainedAt(DateTime $obtainedAt): self
    {
        $this->obtainedAt = $obtainedAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDemandedAt(): ?DateTime
    {
        return $this->demandedAt;
    }

    public function setDemandedAt(DateTime $demandedAt = null): self
    {
        $this->demandedAt = $demandedAt;

        return $this;
    }

    public function getCallProject(): ?CallProject
    {
        return $this->callProject;
    }

    public function setCallProject(CallProject $callProject = null): void
    {
        $this->callProject = $callProject;
    }

    public function getDevise(): ?Devise
    {
        return $this->devise;
    }

    public function setDevise(Devise $devise): void
    {
        $this->devise = $devise;
    }

    public function getFunder(): ?Funder
    {
        return $this->funder;
    }

    public function setFunder(Funder $funder): void
    {
        $this->funder = $funder;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
