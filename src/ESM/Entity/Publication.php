<?php

namespace App\ESM\Entity;

use App\ESM\Entity\DropdownList\CommunicationType;
use App\ESM\Entity\DropdownList\Congress;
use App\ESM\Entity\DropdownList\IsCongress;
use App\ESM\Entity\DropdownList\Journals;
use App\ESM\Entity\DropdownList\PostType;
use App\ESM\Repository\PublicationRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PublicationRepository::class)
 */
class Publication implements AuditrailableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Project
     */
    private $project;

    /**
     * @ORM\Column(type="text", length=255, nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $date;
    /**
     * @ORM\ManyToOne(targetEntity=PostType::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $postType;

    /**
     * @ORM\ManyToOne(targetEntity=IsCongress::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $isCongress;

    /**
     * @ORM\ManyToOne(targetEntity=Journals::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $journals;

    /**
     * @ORM\ManyToOne(targetEntity=Congress::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $congress;

    /**
     * @ORM\ManyToOne(targetEntity=CommunicationType::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $communicationType;

    /**
     * @ORM\Column(type="string", length=55, nullable=true)
     */
    private $postNumber;

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

    public function __toString(): ?string
    {
        return $this->postType;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(?DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getPostType(): ?PostType
    {
        return $this->postType;
    }

    public function setPostType(PostType $postType): void
    {
        $this->postType = $postType;
    }

    public function getIsCongress(): ?IsCongress
    {
        return $this->isCongress;
    }

    public function setIsCongress(?IsCongress $isCongress): void
    {
        $this->isCongress = $isCongress;
    }

    public function getJournals(): ?Journals
    {
        return $this->journals;
    }

    public function setJournals(Journals $journals): void
    {
        $this->journals = $journals;
    }

    public function getCongress(): ?Congress
    {
        return $this->congress;
    }

    public function setCongress(Congress $congress): void
    {
        $this->congress = $congress;
    }

    public function getCommunicationType(): ?CommunicationType
    {
        return $this->communicationType;
    }

    public function setCommunicationType(CommunicationType $communicationType): void
    {
        $this->communicationType = $communicationType;
    }

    public function getPostNumber(): ?string
    {
        return $this->postNumber;
    }

    public function setPostNumber(?string $postNumber): self
    {
        $this->postNumber = $postNumber;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment ?? '';
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
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
