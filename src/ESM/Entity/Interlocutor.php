<?php

namespace App\ESM\Entity;

use App\ESM\Entity\DropdownList\Civility;
use App\ESM\Entity\DropdownList\Cooperator;
use App\ESM\Entity\DropdownList\ParticipantJob;
use App\ESM\Entity\DropdownList\ParticipantSpeciality;
use App\ESM\Repository\InterlocutorRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=InterlocutorRepository::class)
 * @UniqueEntity(
 *     fields={"email"},
 *     message="L'email de l'interlocuteur est déjà utilisé",
 * )
 */
class Interlocutor implements AuditrailableInterface
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
     * @ORM\ManyToOne(targetEntity=Civility::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Civility|null
     */
    private $civility;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="entity.Participant.field.notBlank.firstName")
     *
     * @var string
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="entity.Participant.field.notBlank.lastName")
     *
     * @var string
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\Email
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=true, length=20)
     * @var string
     */
    private $phone;

    /**
     * @ORM\Column(type="string", nullable=true, length=20)
     * @var string|null
     */
    private $fax;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $deletedAt;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     * @Assert\Length(min=11, max=11, exactMessage="Le N°RPPS doit comporter 11 chiffres")
     *
     * @var string
     */
    private $rppsNumber;

    /**
     * @ORM\Column(type="string", nullable=true, length=20)
     *
     * @var string|null
     */
    private $amNumber;

	/**
	 * @ORM\ManyToMany(targetEntity=Cooperator::class, inversedBy="interlocutors", orphanRemoval=true)
	 * @ORM\JoinColumn(nullable=false)
	 * @ORM\OrderBy({"title" = "ASC"})
	 *
	 *
	 * @var ArrayCollection<int, Cooperator>
	 */
	private $cooperators;

    /**
     * @ORM\OneToMany(targetEntity=InterlocutorCenter::class, mappedBy="interlocutor")
     *
     * @var ArrayCollection<int, InterlocutorCenter>
     */
    private $interlocutorCenters;

    /**
     * @ORM\ManyToOne(targetEntity=ParticipantJob::class)
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="entity.Participant.field.notBlank.job")
     */
    private $job;

    /**
     * @ORM\ManyToOne(targetEntity=ParticipantSpeciality::class)
     * @ORM\JoinColumn(nullable=true)
     *
     * @var ParticipantSpeciality|null
     */
    private $specialtyOne;

    /**
     * @ORM\ManyToMany(targetEntity=Institution::class, inversedBy="interlocutors")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\OrderBy({"name" = "ASC"})
     *
     * @Assert\Count(min="1")
     *
     * @var ArrayCollection<int, Institution>
     */
    private $institutions;

    /**
     * @ORM\ManyToOne(targetEntity=ParticipantSpeciality::class)
     * @ORM\JoinColumn(nullable=true)
     *
     * @var ParticipantSpeciality|null
     */
    private $specialtyTwo;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=DocumentTransverse::class, mappedBy="interlocutor")
     */
    private $documentTransverses;

    /**
     * @ORM\OneToMany(targetEntity=DeviationAction::class, mappedBy="interlocutor")
     * @var ArrayCollection<int, DeviationAction>
     */
    private $deviationActions;

    /**
     * @ORM\OneToMany(targetEntity=DeviationReviewAction::class, mappedBy="interlocutor")
     * @var ArrayCollection<int, DeviationReviewAction>
     */
    private $deviationReviewActions;

    public function __construct()
    {
        $this->cooperators            = new ArrayCollection();
        $this->institutions           = new ArrayCollection();
        $this->interlocutorCenters    = new ArrayCollection();
        $this->documentTransverses    = new ArrayCollection();
        $this->deviationReviewActions = new ArrayCollection();
    }

	/**
	 * @return int|null
	 */
    public function getId(): ?int
    {
        return $this->id;
    }

	/**
	 * @return string
	 */
    public function getFullName(): string
    {
        return (null === $this->getCivility() ? '' : $this->civility.' ')
            .$this->firstName.' '.$this->lastName;
    }

	/**
	 * @return string[]
	 */
    public function getFieldsToBeIgnored(): array
    {
        return ['createdAt'];
    }

	/**
	 * @return string
	 */
    public function __toString(): string
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

	/**
	 * @return Civility|null
	 */
    public function getCivility(): ?Civility
    {
        return $this->civility;
    }

	/**
	 * @param Civility|null $civility
	 */
    public function setCivility(?Civility $civility): void
    {
        $this->civility = $civility;
    }

	/**
	 * @return string|null
	 */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

	/**
	 * @param string $firstName
	 */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

	/**
	 * @return string|null
	 */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

	/**
	 * @param string $lastName
	 */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

	/**
	 * @return string|null
	 */
    public function getEmail(): ?string
    {
        return $this->email;
    }

	/**
	 * @param string $email
	 */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

	/**
	 * @return DateTime|null
	 */
    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

	/**
	 * @param DateTime|null $deletedAt
	 * @return $this
	 */
    public function setDeletedAt(?DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

	/**
	 * @return string|null
	 */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

	/**
	 * @param string $phone
	 */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

	/**
	 * @return string|null
	 */
    public function getRppsNumber(): ?string
    {
        return $this->rppsNumber;
    }

	/**
	 * @param string|null $rppsNumber
	 */
    public function setRppsNumber(?string $rppsNumber): void
    {
        $this->rppsNumber = $rppsNumber;
    }

	/**
	 * @return string|null
	 */
    public function getAmNumber(): ?string
    {
        return $this->amNumber;
    }

	/**
	 * @param string|null $amNumber
	 */
    public function setAmNumber(?string $amNumber): void
    {
        $this->amNumber = $amNumber;
    }

	/**
	 * @return string|null
	 */
    public function getFax(): ?string
    {
        return $this->fax;
    }

	/**
	 * @param string|null $fax
	 */
    public function setFax(?string $fax): void
    {
        $this->fax = $fax;
    }

	/**
	 * @return ParticipantJob|null
	 */
    public function getJob(): ?ParticipantJob
    {
        return $this->job;
    }

	/**
	 * @param ParticipantJob $job
	 */
    public function setJob(ParticipantJob $job): void
    {
        $this->job = $job;
    }

	/**
	 * @return ParticipantSpeciality|null
	 */
    public function getSpecialtyOne(): ?ParticipantSpeciality
    {
        return $this->specialtyOne;
    }

	/**
	 * @param ParticipantSpeciality|null $specialtyOne
	 */
    public function setSpecialtyOne(?ParticipantSpeciality $specialtyOne): void
    {
        $this->specialtyOne = $specialtyOne;
    }

	/**
	 * @return ParticipantSpeciality|null
	 */
    public function getSpecialtyTwo(): ?ParticipantSpeciality
    {
        return $this->specialtyTwo;
    }

	/**
	 * @param ParticipantSpeciality|null $specialtyTwo
	 */
    public function setSpecialtyTwo(?ParticipantSpeciality $specialtyTwo): void
    {
        $this->specialtyTwo = $specialtyTwo;
    }

	/**
	 * @return ArrayCollection<int, Cooperator>
	 */
	public function getCooperators(): Collection
	{
		return $this->cooperators;
	}

	/**
	 * @param Cooperator $cooperator
	 * @return $this
	 */
	public function addCooperator(Cooperator $cooperator): self
	{
		if (!$this->cooperators->contains($cooperator)) {
			$this->cooperators[] = $cooperator;
		}

		return $this;
	}

	/**
	 * @param Cooperator $cooperator
	 * @return $this
	 */
	public function removeCooperator(Cooperator $cooperator): self
	{
		if ($this->cooperators->contains($cooperator)) {
			$this->cooperators->removeElement($cooperator);
		}

		return $this;
	}

    /**
     * @return ArrayCollection<int, Institution>
     */
    public function getInstitutions(): Collection
    {
        return $this->institutions;
    }

	/**
	 * @param Institution $institution
	 * @return $this
	 */
    public function addInstitution(Institution $institution): self
    {
        if (!$this->institutions->contains($institution)) {
            $this->institutions[] = $institution;
        }

        return $this;
    }

	/**
	 * @param Institution $institution
	 * @return $this
	 */
    public function removeInstitution(Institution $institution): self
    {
        if ($this->institutions->contains($institution)) {
            $this->institutions->removeElement($institution);
        }

        return $this;
    }

	/**
	 * @param Institution $institution
	 * @return bool
	 */
    public function hasInstitution(Institution $institution): bool
    {
        return $this->institutions->contains($institution);
    }

	/**
	 * @return string
	 */
    public function displayName(): string
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

	/**
	 * @return DateTime|null
	 */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

	/**
	 * @param DateTime $createdAt
	 */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return ArrayCollection<int, InterlocutorCenter>
     */
    public function getInterlocutorCenters(): Collection
    {
        return $this->interlocutorCenters;
    }

	/**
	 * @param InterlocutorCenter $interlocutorCenter
	 * @return $this
	 */
	public function addInterlocutorCenter(InterlocutorCenter $interlocutorCenter): self
	{
		if (!$this->interlocutorCenters->contains($interlocutorCenter)) {
			$this->interlocutorCenters[] = $interlocutorCenter;
			$interlocutorCenter->setInterlocutor($this);
		}

		return $this;
	}

	/**
	 * @param InterlocutorCenter $interlocutorCenter
	 * @return $this
	 */
	public function removeInterlocutorCenter(InterlocutorCenter $interlocutorCenter): self
	{
		if ($this->interlocutorCenters->removeElement($interlocutorCenter)) {
			$interlocutorCenter->setInterlocutor(null);
		}

		return $this;
	}

    /**
     * @return Collection|DocumentTransverse[]
     */
    public function getDocumentTransverses(): Collection
    {
        return $this->documentTransverses;
    }

    /**
     * @param DocumentTransverse $documentTransverse
     * @return $this
     */
    public function addDocumentTransverse(DocumentTransverse $documentTransverse): self
    {
        if (!$this->documentTransverses->contains($documentTransverse)) {
            $this->documentTransverses[] = $documentTransverse;
            $documentTransverse->addInterlocutor($this);
        }

        return $this;
    }

    /**
     * @param DocumentTransverse $documentTransverse
     * @return $this
     */
    public function removeDocumentTransverse(DocumentTransverse $documentTransverse): self
    {
        if ($this->documentTransverses->removeElement($documentTransverse)) {
            $documentTransverse->removeInterlocutor($this);
        }

        return $this;
    }

    /**
     * @return Collection|DeviationAction[]
     */
    public function getDeviationActions(): Collection
    {
        return $this->deviationActions;
    }

    /**
     * @param DeviationAction $deviationAction
     * @return $this
     */
    public function addDeviationAction(DeviationAction $deviationAction): self
    {
        if (!$this->deviationActions->contains($deviationAction)) {
            $this->deviationActions[] = $deviationAction;
            $deviationAction->setInterlocutor($this);
        }

        return $this;
    }

    /**
     * @param DeviationAction $deviationAction
     * @return $this
     */
    public function removeDeviationAction(DeviationAction $deviationAction): self
    {
        if ($this->deviationActions->removeElement($deviationAction)) {
            $deviationAction->setInterlocutor(null);
        }

        return $this;
    }

    /**
     * @return Collection|DeviationReviewAction[]
     */
    public function getDeviationReviewActions(): Collection
    {
        return $this->deviationReviewActions;
    }

    /**
     * @param DeviationReviewAction $deviationReviewAction
     * @return $this
     */
    public function addDeviationReviewAction(DeviationReviewAction $deviationReviewAction): self
    {
        if (!$this->deviationReviewActions->contains($deviationReviewAction)) {
            $this->deviationReviewActions[] = $deviationReviewAction;
            $deviationReviewAction->setInterlocutor($this);
        }

        return $this;
    }

    /**
     * @param DeviationReviewAction $deviationReviewAction
     * @return $this
     */
    public function removeDeviationReviewAction(DeviationReviewAction $deviationReviewAction): self
    {
        if ($this->deviationReviewActions->removeElement($deviationReviewAction)) {
            $deviationReviewAction->setInterlocutor(null);
        }

        return $this;
    }
}
