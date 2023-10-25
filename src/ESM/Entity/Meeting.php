<?php

namespace App\ESM\Entity;

use App\ESM\Entity\DropdownList\MeetingType;
use App\ESM\Repository\MeetingRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=MeetingRepository::class)
 * @Vich\Uploadable
 */
class Meeting implements AuditrailableInterface
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
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=MeetingType::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $object;

    /**
     * @ORM\ManyToMany(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\Count(min="1")
     *
     * @var ArrayCollection<int, User>
     */
    private $users;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    private $startedAt;

    /**
     * @Assert\File
     * @Vich\UploadableField(mapping="meeting_file", fileNameProperty="report")
     *
     * @var File|null
     */
    private $reportFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string|null
     */
    private $report;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $duration;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $startedHour;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var User
     */
    private $createdBy;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $closedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Project
     */
    private $project;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFieldsToBeIgnored(): array
    {
        return ['project', 'updatedAt'];
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getObject(): ?MeetingType
    {
        return $this->object;
    }

    public function setObject(MeetingType $object): void
    {
        $this->object = $object;
    }

    /**
     * @return ArrayCollection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }

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

    public function getStartedAt(): ?DateTime
    {
        return $this->startedAt;
    }

    public function setStartedAt(DateTime $startedAt): void
    {
        $this->startedAt = $startedAt;
    }

    public function getReport(): ?string
    {
        return $this->report;
    }

    public function setReport(?string $report): void
    {
        $this->report = $report;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(?string $duration): void
    {
        $this->duration = $duration;
    }

    public function getStartedHour(): ?string
    {
        return $this->startedHour;
    }

    public function setStartedHour(string $startedHour): self
    {
        $this->startedHour = $startedHour;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getClosedAt(): ?DateTime
    {
        return $this->closedAt;
    }

    public function setClosedAt(?DateTime $closedAt): self
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    public function getReportFile(): ?File
    {
        return $this->reportFile;
    }

    public function setReportFile(?File $reportFile): Meeting
    {
        $this->reportFile = $reportFile;
        if ($this->reportFile instanceof UploadedFile) {
            $this->updatedAt = new \DateTime();
        }

        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
