<?php

namespace App\ESM\Entity;

use App\ESM\Entity\DropdownList\TrainingType;
use App\ESM\Repository\TrainingRepository;
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
 * @ORM\Entity(repositoryClass=TrainingRepository::class)
 * @Vich\Uploadable
 */
class Training implements AuditrailableInterface
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
    private $title;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    private $startedAt;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThanOrEqual(
     *     propertyPath="startedAt",
     *     message="La date de fin doit être supérieure ou égale de la date de début"
     * )
     *
     * @var DateTime
     */
    private $endedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Project
     */
    private $project;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     *
     * @var string
     */
    private $duration;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @Assert\File
     * @Vich\UploadableField(mapping="training_file", fileNameProperty="material")
     *
     * @var File|null
     */
    private $materialFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string|null
     */
    private $material;

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
     * @ORM\ManyToOne(targetEntity=TrainingType::class)
     * @ORM\JoinColumn(nullable=true)
     *
     * @var TrainingType|null
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $startedHour;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $endedHour;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var User
     */
    private $teacher;

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
        return ['project'];
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getStartedAt(): ?DateTime
    {
        return $this->startedAt;
    }

    public function setStartedAt(DateTime $startedAt): void
    {
        $this->startedAt = $startedAt;
    }

    public function getEndedAt(): ?DateTime
    {
        return $this->endedAt;
    }

    public function setEndedAt(DateTime $endedAt): void
    {
        $this->endedAt = $endedAt;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    public function getType(): TrainingType
    {
        return $this->type;
    }

    public function setType(?TrainingType $type): void
    {
        $this->type = $type;
    }

    public function getMaterial(): ?string
    {
        return $this->material;
    }

    public function setMaterial(?string $material): void
    {
        $this->material = $material;
    }

    public function getMaterialFile(): ?File
    {
        return $this->materialFile;
    }

    public function setMaterialFile(?File $materialFile): Training
    {
        $this->materialFile = $materialFile;
        if ($this->materialFile instanceof UploadedFile) {
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

    public function setStartedHour(?string $startedHour): self
    {
        $this->startedHour = $startedHour;

        return $this;
    }

    public function getEndedHour(): ?string
    {
        return $this->endedHour;
    }

    public function setEndedHour(?string $endedHour): self
    {
        $this->endedHour = $endedHour;

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

    public function getClosedAt(): ?DateTime
    {
        return $this->closedAt;
    }

    public function setClosedAt(?DateTime $closedAt): self
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    /**
     * @return User
     */
    public function getTeacher(): ?User
    {
        return $this->teacher;
    }

    public function setTeacher(User $teacher): void
    {
        $this->teacher = $teacher;
    }
}
