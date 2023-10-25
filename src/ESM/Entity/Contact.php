<?php

namespace App\ESM\Entity;

use App\ESM\Entity\DropdownList\ContactObject;
use App\ESM\Entity\DropdownList\ContactPhase;
use App\ESM\Entity\DropdownList\ContactType;
use App\ESM\Entity\DropdownList\ContactTypeRecipient;
use App\ESM\Repository\ContactRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="contact")
 * @ORM\Entity(repositoryClass=ContactRepository::class)
 */
class Contact
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
     * @ORM\ManyToOne(targetEntity=ContactObject::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var ContactObject
     */
    private $object;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 * @var string|null
	 */
	private $objectReason;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $detail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $cc;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Project
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity=ContactType::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var ContactType
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=ContactPhase::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var ContactPhase
     */
    private $phase;

    /**
     * @ORM\ManyToOne(targetEntity=ContactTypeRecipient::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var ContactTypeRecipient
     */
    private $typeRecipient;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $hour;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @var bool
     */
    private $completed = false;

    /**
     * @ORM\ManyToMany(targetEntity=Interlocutor::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var ArrayCollection<int, Interlocutor>
     */
    private $interlocutors;

    /**
     * @ORM\ManyToMany(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var ArrayCollection<int, User>
     */
    private $intervenants;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var User
     */
    private $intervenant;

    public function __construct()
    {
        $this->interlocutors = new ArrayCollection();
        $this->intervenants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObject(): ?ContactObject
    {
        return $this->object;
    }

    public function setObject(ContactObject $object): void
    {
        $this->object = $object;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(string $detail): void
    {
        $this->detail = $detail;
    }

    public function getCC(): ?string
    {
        return $this->cc;
    }

    public function setCC(string $cc): void
    {
        $this->cc = $cc;
    }

    public function getType(): ?ContactType
    {
        return $this->type;
    }

    public function setType(ContactType $type): void
    {
        $this->type = $type;
    }

	/**
	 * @return string|null
	 */
	public function getObjectReason(): ?string
	{
		return $this->objectReason;
	}

	/**
	 * @param string|null $objectReason
	 */
	public function setObjectReason(?string $objectReason): void
	{
		$this->objectReason = $objectReason;
	}

    public function getPhase(): ?ContactPhase
    {
        return $this->phase;
    }

    public function setPhase(ContactPhase $phase): void
    {
        $this->phase = $phase;
    }

    public function getTypeRecipient(): ?ContactTypeRecipient
    {
        return $this->typeRecipient;
    }

    public function setTypeRecipient(ContactTypeRecipient $typeRecipient): void
    {
        $this->typeRecipient = $typeRecipient;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $createdAt): void
    {
        $this->date = $createdAt;
    }

    public function getHour(): ?string
    {
        return $this->hour;
    }

    public function setHour(?string $hour): self
    {
        $this->hour = $hour;

        return $this;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): void
    {
        $this->completed = $completed;
    }

    /**
     * @return ArrayCollection<int, Interlocutor>
     */
    public function getInterlocutors(): Collection
    {
        return $this->interlocutors;
    }

    public function addInterlocutor(Interlocutor $interlocutor): self
    {
        if (!$this->interlocutors->contains($interlocutor)) {
            $this->interlocutors[] = $interlocutor;
        }

        return $this;
    }

    public function removeInterlocutor(Interlocutor $interlocutor): self
    {
        if ($this->interlocutors->contains($interlocutor)) {
            $this->interlocutors->removeElement($interlocutor);
        }

        return $this;
    }

    public function resetInterlocutors()
    {
        foreach ($this->interlocutors as $interlocutor) {
            $this->interlocutors->removeElement($interlocutor);
        }
    }

    /**
     * @return ArrayCollection<int, User>
     */
    public function getIntervenants(): Collection
    {
        return $this->intervenants;
    }

    public function addIntervenant(User $intervenant): self
    {
        if (!$this->intervenants->contains($intervenant)) {
            $this->intervenants[] = $intervenant;
        }

        return $this;
    }

    public function removeIntervenant(User $intervenant): self
    {
        if ($this->intervenants->contains($intervenant)) {
            $this->intervenants->removeElement($intervenant);
        }

        return $this;
    }

    public function resetIntervenants()
    {
        foreach ($this->intervenants as $intervenant) {
            $this->intervenants->removeElement($intervenant);
        }
    }

    /**
     * @return User
     */
    public function getIntervenant(): ?User
    {
        return $this->intervenant;
    }

    public function setIntervenant(User $intervenant): void
    {
        $this->intervenant = $intervenant;
    }
}
