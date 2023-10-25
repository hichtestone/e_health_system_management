<?php

namespace App\ESM\Entity;

use App\ESM\Entity\DropdownList\Civility;
use App\ESM\Entity\DropdownList\Department;
use App\ESM\Entity\DropdownList\Society;
use App\ESM\Entity\DropdownList\UserJob;
use App\ETMF\Entity\Mailgroup;
use App\ETMF\Entity\DocumentVersion;
use App\ESM\Repository\UserRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use App\ESM\Service\AuditTrail\AuditTrailAssociableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email", message="Un utilisateur ayant cette adresse mail existe déjà")
 */
class User implements UserInterface, \Serializable, AuditrailableInterface, AuditTrailAssociableInterface
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 *
	 * @var int
     * @Groups({"userProject"})
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=100, unique=true)
	 * @Assert\Email(message="Le format de l'adresse email doit être valide")
	 * @Assert\NotBlank(message="L'adresse email de l'utilisateur est obligatoire")
	 *
	 * @var string
	 */
	private $email;

	/**
	 * @var TermsOfServiceSignature[]
	 * @ORM\OneToMany(targetEntity="App\ESM\Entity\TermsOfServiceSignature", mappedBy="user")
	 */
	private $terms_of_service_signatures;

	/**
	 * @ORM\Column(type="json")
	 *
	 * @var array
	 */
	private $roles = [];

	/**
	 * @ORM\Column(type="string", nullable=true, length=255)
	 *
	 * @var string
	 */
	private $password;

	/**
	 * @ORM\Column(type="string", length=100)
	 * @Assert\NotBlank(message="Le prénom de l'userProject projet est obligatoire")
	 * @Assert\Length(min=3, minMessage="Le nom prénom de l'userProject projet doit faire entre 3 et 255 caractères", max=255, maxMessage="Le prénom de l'userProject projet doit faire entre 3 et 255 caractères")
	 *
	 * @var string
     * @Groups({"userProject"})
	 */
	private $firstName;

	/**
	 * @ORM\Column(type="string", length=100)
	 * @Assert\NotBlank(message="Le nom de l'userProject projet est obligatoire")
	 * @Assert\Length(min=3, minMessage="Le nom de l'userProject projet doit faire entre 3 et 255 caractères", max=255, maxMessage="Le nom de l'userProject projet doit faire entre 3 et 255 caractères")
	 *
	 * @var string
     * @Groups({"userProject"})
	 */
	private $lastName;

	/**
	 * @ORM\Column(type="string", length=2)
	 *
	 * @var string
	 */
	private $locale = 'fr';

	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 *
	 * @var bool|null
	 */
	private $isSuperAdmin;

	/**
	 * @ORM\ManyToOne(targetEntity=Civility::class)
	 * @ORM\JoinColumn(nullable=true)
	 *
	 * @var Civility|null
	 */
	private $civility;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 *
	 * @var DateTime|null
	 */
	private $deletedAt;

	/**
	 * @ORM\ManyToOne(targetEntity=Profile::class, inversedBy="users")
	 * @ORM\JoinColumn(nullable=false)
	 *
	 * @var Profile
	 */
	private $profile;

	/**
	 * @ORM\ManyToOne(targetEntity=UserJob::class)
	 * @ORM\JoinColumn(nullable=false)
	 *
	 * @var UserJob
	 */
	private $job;

	/**
	 * @ORM\ManyToOne(targetEntity=Department::class)
	 *
	 * @var Department|null
	 */
	private $department;

	/**
	 * @ORM\Column(type="boolean")
	 *
	 * @var bool
	 */
	private $hasAccessEsm = false;

	/**
	 * @ORM\Column(type="boolean")
	 *
	 * @var bool
	 */
	private $hasAccessEtmf = false;

	/**
	 * @ORM\OneToMany(targetEntity=UserProject::class, mappedBy="user")
	 *
	 * @var ArrayCollection<int, UserProject>
	 */
	private $userProjects;

	/**
	 * @ORM\Column(type="string", length=255)
	 * @Assert\Length(min = 10, max = 10, minMessage = "Minimum 10 chiffres", maxMessage = "Maximun 10 chiffres")
	 * @Assert\Regex(pattern="/^[0-9]*$/", message="Le numéro de téléphone doit avoir exactement 10 chiffres")
	 */
	private $phone;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $note;

    /**
     * @ORM\ManyToOne(targetEntity=Society::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Society|null
     */
    private $society;

	/**
	 * @var DateTime|null
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $password_updated_at;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $reset_password_token;

	/**
	 * @var DateTime|null
	 * @ORM\Column(type="datetime", length=255, nullable=true)
	 */
	private $reset_password_at;

	/**
	 * @var bool
	 * @ORM\Column(type="boolean")
	 */
	private $is_bot = false;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", length=128, nullable=true)
	 */
	private $twoFactorSecret;

	/**
	 * @ORM\OneToMany(targetEntity=Deviation::class, mappedBy="declaredBy")
	 * @var ArrayCollection<int, Deviation>
	 */
	private $deviations;

	/**
	 * @ORM\OneToMany(targetEntity=DeviationSystem::class, mappedBy="referentQA")
	 * @var ArrayCollection<int, DeviationSystem>
	 */
	private $deviationsSystem;

	/**
	 * @ORM\OneToMany(targetEntity=DeviationSystem::class, mappedBy="officialQA")
	 * @var ArrayCollection<int, DeviationSystem>
	 */
	private $deviationsSystemOfficial;

	/**
	 * @ORM\OneToMany(targetEntity=DeviationSample::class, mappedBy="declaredBy")
	 *
	 * @var ArrayCollection<int, DeviationSample>
	 */
	private $deviationSamples;

	/**
	 * @ORM\OneToMany(targetEntity=DeviationAction::class, mappedBy="intervener")
	 * @var ArrayCollection<int, DeviationAction>
	 */
	private $deviationActions;

	/**
	 * @ORM\OneToMany(targetEntity=DeviationSampleAction::class, mappedBy="user")
	 * @var ArrayCollection<int, DeviationSampleAction>
	 */
	private $deviationSampleActions;

	/**
	 * @ORM\OneToMany(targetEntity=DeviationReviewAction::class, mappedBy="intervener")
	 * @var ArrayCollection<int, DeviationReviewAction>
	 */
	private $deviationReviewActions;

	/**
	 * @ORM\OneToMany(targetEntity=DeviationSystemAction::class, mappedBy="intervener")
	 * @var ArrayCollection<int, DeviationSystemAction>
	 */
	private $deviationSystemActions;

	/**
	 * @ORM\OneToMany(targetEntity=DeviationSystemReviewAction::class, mappedBy="intervener")
	 * @var ArrayCollection<int, DeviationSystemReviewAction>
	 */
	private $deviationSystemReviewActions;

	/**
	 * @ORM\OneToMany(targetEntity=DeviationReview::class, mappedBy="reader")
	 * @var ArrayCollection<int, DeviationReview>
	 */
	private $deviationReviews;

	/**
	 * @ORM\OneToMany(targetEntity=ReportConfigVersion::class, mappedBy="configuredBy")
	 * @var ArrayCollection<int, ReportConfigVersion>
	 */
	private $reportConfigVersions;

	/**
	 * @ORM\ManyToMany(targetEntity=Mailgroup::class, mappedBy="users")
	 * @ORM\JoinTable(name="users_mailgroups")
	 *
	 * @var Collection<int, Mailgroup>
	 */
	private $mailgroups;

	/**
	 * @ORM\OneToMany(targetEntity=DocumentVersion::class, mappedBy="validatedQaBy")
     * @ORM\JoinColumn(nullable=false)
	 * @var ArrayCollection<int, DocumentVersion>
	 */
	private $documentVersionsValidatedQaBy;

    /**
     * @ORM\OneToMany(targetEntity=DocumentVersion::class, mappedBy="author")
     * @ORM\JoinColumn(nullable=false)
     * @var ArrayCollection<int, DocumentVersion>
     */
    private $documentVersionsAuthor;

    /**
     * @ORM\OneToMany(targetEntity=DocumentVersion::class, mappedBy="createdBy")
     * @ORM\JoinColumn(nullable=false)
     * @var ArrayCollection<int, DocumentVersion>
     */
    private $documentVersionsCreatedBy;

	/**
	 * User constructor.
	 */
	public function __construct()
	{
        $this->deviations = new ArrayCollection();
        $this->deviationSamples = new ArrayCollection();
        $this->deviationActions = new ArrayCollection();
        $this->deviationSystemActions = new ArrayCollection();
        $this->deviationReviewActions = new ArrayCollection();
        $this->deviationSystemReviewActions = new ArrayCollection();
        $this->deviationSampleActions = new ArrayCollection();
        $this->reportConfigVersions = new ArrayCollection();
        $this->mailgroups = new ArrayCollection();
        $this->documentVersionsValidatedQaBy = new ArrayCollection();
        $this->documentVersionsAuthor = new ArrayCollection();
        $this->documentVersionsCreatedBy = new ArrayCollection();
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
	public function getAuditTrailString(): string
	{
		return $this->getFirstName() . ' ' . $this->getLastName();
	}

	/**
	 * @return string[]
	 */
	public function getFieldsToBeIgnored(): array
	{
		return ['password', 'roles', 'isSuperAdmin', 'reset_password_token'];
	}

	/**
	 * @param UserInterface $user
	 * @return bool
	 */
	public function isEqualTo(UserInterface $user): bool
	{
		return $this->email === $user->getUsername();
	}

	/**
	 * @return string|null
	 */
	public function getTwoFactorSecret(): ?string
	{
		return $this->twoFactorSecret;
	}

	/**
	 * @param string|null $twoFactorSecret
	 */
	public function setTwoFactorSecret(?string $twoFactorSecret): void
	{
		$this->twoFactorSecret = $twoFactorSecret;
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
	 * @return $this
	 */
	public function setEmail(string $email): self
	{
		$this->email = $email;

		return $this;
	}

	/**
	 * A visual identifier that represents this user.
	 *
	 * @see UserInterface
	 */
	public function getUsername(): string
	{
		return $this->email;
	}

	/**
	 * @see UserInterface
	 */
	public function getRoles(): array
	{
		$roles = $this->profile->getSecurity();
		if ($this->isSuperAdmin) {
			$roles[] = 'ROLE_SUPER_ADMIN';
		}
		if (!in_array('ROLE_USER', $roles, true)) {
			$roles[] = 'ROLE_USER';
		}

		return array_unique($roles);
	}

	/**
	 * @param array $roles
	 * @return $this
	 */
	public function setRoles(array $roles): self
	{
		$this->roles = $roles;

		return $this;
	}

	/**
	 * @see UserInterface
	 */
	public function getPassword(): string
	{
		return $this->password;
	}

	/**
	 * @param string $password
	 * @return $this
	 */
	public function setPassword(string $password): self
	{
		$this->password = $password;

		return $this;
	}

	/**
	 * @see UserInterface
	 */
	public function getSalt(): ?string
	{
		// not needed when using the "bcrypt" algorithm in security.yaml
		return null;
	}

	/**
	 * @see UserInterface
	 */
	public function eraseCredentials(): void
	{
		// If you store any temporary, sensitive data on the user, clear it here
		// $this->plainPassword = null;
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
	 * @return $this
	 */
	public function setFirstName(string $firstName): self
	{
		$this->firstName = $firstName;

		return $this;
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
	 * @return $this
	 */
	public function setLastName(string $lastName): self
	{
		$this->lastName = $lastName;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getLocale(): ?string
	{
		return $this->locale;
	}

	/**
	 * @param string $locale
	 * @return $this
	 */
	public function setLocale(string $locale): self
	{
		$this->locale = $locale;

		return $this;
	}

	/**
	 * @return bool|null
	 */
	public function getIsSuperAdmin(): ?bool
	{
		return $this->isSuperAdmin;
	}

	/**
	 * @param bool|null $isSuperAdmin
	 * @return $this
	 */
	public function setIsSuperAdmin(?bool $isSuperAdmin): self
	{
		$this->isSuperAdmin = $isSuperAdmin;

		return $this;
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
	public function displayName(): ?string
	{
		return $this->getFirstName() . ' ' . $this->getLastName();
	}

	/**
	 * @return Profile|null
	 */
	public function getProfile(): ?Profile
	{
		return $this->profile;
	}

	/**
	 * @param Profile|null $profile
	 * @return $this
	 */
	public function setProfile(?Profile $profile): self
	{
		$this->profile = $profile;

		return $this;
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
	 * @param bool $hasAccessEsm
	 */
	public function setHasAccessEsm(bool $hasAccessEsm): void
	{
		$this->hasAccessEsm = $hasAccessEsm;
	}

	/**
	 * @param bool $hasAccessEtmf
	 */
	public function setHasAccessEtmf(bool $hasAccessEtmf): void
	{
		$this->hasAccessEtmf = $hasAccessEtmf;
	}

	/**
	 * @return UserJob|null
	 */
	public function getJob(): ?UserJob
	{
		return $this->job;
	}

	/**
	 * @param UserJob|null $job
	 */
	public function setJob(?UserJob $job): void
	{
		$this->job = $job;
	}

	/**
	 * @return Department|null
	 */
	public function getDepartment(): ?Department
	{
		return $this->department;
	}

	/**
	 * @param Department|null $department
	 */
	public function setDepartment(?Department $department): void
	{
		$this->department = $department;
	}

	/**
	 * @return bool|null
	 */
	public function getHasAccessESM(): ?bool
	{
		return $this->hasAccessEsm;
	}

	/**
	 * @return bool|null
	 */
	public function getHasAccessEtmf(): ?bool
	{
		return $this->hasAccessEtmf;
	}

	/**
	 * @return string|null
	 */
	public function __toString(): string
	{
		return $this->displayName();
	}

	/**
	 * @return ArrayCollection<int, UserProject>
	 */
	public function getUserProjects(): Collection
	{
		return $this->userProjects;
	}

	/**
	 * @param UserProject $userProjects
	 * @return $this
	 */
	public function addUserProject(UserProject $userProjects): self
	{
		if (!$this->userProjects->contains($userProjects)) {
			$this->userProjects[] = $userProjects;
		}

		return $this;
	}

	/**
	 * @param UserProject $userProjects
	 * @return $this
	 */
	public function removeUserProject(UserProject $userProjects): self
	{
		if ($this->userProjects->contains($userProjects)) {
			$this->userProjects->removeElement($userProjects);
		}

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
	 * @return $this
	 */
	public function setPhone(string $phone): self
	{
		$this->phone = $phone;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getNote(): ?string
	{
		return $this->note;
	}

	/**
	 * @return Society|null
	 */
	public function getSociety(): ?Society
	{
		return $this->society;
	}

	/**
	 * @param Society|null $society
	 */
	public function setSociety(?Society $society): void
	{
		$this->society = $society;
	}

	/**
	 * @param string|null $note
	 * @return $this
	 */
	public function setNote(?string $note): self
	{
		$this->note = $note;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getFullName(): string
	{
		return (null === $this->getCivility() ? '' : $this->civility . ' ')
			. $this->firstName . ' ' . $this->lastName;
	}

	/**
	 * @return DateTime|null
	 */
	public function getPasswordUpdatedAt(): ?DateTime
	{
		return $this->password_updated_at;
	}

	/**
	 * @param mixed $password_updated_at
	 */
	public function setPasswordUpdatedAt($password_updated_at): void
	{
		$this->password_updated_at = $password_updated_at;
	}

	/**
	 * @return string|null
	 */
	public function getResetPasswordToken(): ?string
	{
		return $this->reset_password_token;
	}

	/**
	 * @param mixed $reset_password_token
	 */
	public function setResetPasswordToken($reset_password_token): void
	{
		$this->reset_password_token = $reset_password_token;
	}

	/**
	 * @return DateTime|null
	 */
	public function getResetPasswordAt(): ?DateTime
	{
		return $this->reset_password_at;
	}

	/**
	 * @param mixed $reset_password_at
	 */
	public function setResetPasswordAt($reset_password_at): void
	{
		$this->reset_password_at = $reset_password_at;
	}

	/**
	 * @return TermsOfServiceSignature[]
	 */
	public function getTermsOfServiceSignatures()
	{
		return $this->terms_of_service_signatures;
	}

	/**
	 * @return bool
	 */
	public function isIsBot(): bool
	{
		return $this->is_bot;
	}

	/**
	 * @param bool $is_bot
	 */
	public function setIsBot(bool $is_bot): void
	{
		$this->is_bot = $is_bot;
	}

	/**
	 * @return string
	 */
	public function serialize(): string
	{
		return serialize([
			$this->id,
			$this->email,
			$this->password,
			$this->hasAccessEsm,
			// see section on salt below
			// $this->salt,
		]);
	}

	/**
	 * @param string $serialized
	 */
	public function unserialize($serialized): array
	{
		return list($this->id, $this->email, $this->password, $this->hasAccessEsm,
			// see section on salt below
			// $this->salt
			) = unserialize($serialized, ['allowed_classes' => false]);
	}

	/**
	 * @return Collection|Deviation[]
	 */
	public function getDeviations(): Collection
	{
		return $this->deviations;
	}

	/**
	 * @param Deviation $deviation
	 * @return $this
	 */
	public function addDeviation(Deviation $deviation): self
	{
		if (!$this->deviations->contains($deviation)) {
			$this->deviations[] = $deviation;
			$deviation->setDeclaredBy($this);
		}

		return $this;
	}

	/**
	 * @param Deviation $deviation
	 * @return $this
	 */
	public function removeDeviation(Deviation $deviation): self
	{
		if ($this->deviations->removeElement($deviation)) {
			$deviation->setDeclaredBy(null);
		}

		return $this;
	}

	/**
	 * @return Collection|DeviationSystem[]
	 */
	public function getDeviationsSystem(): Collection
	{
		return $this->deviationsSystem;
	}

	/**
	 * @param DeviationSystem $deviationSystem
	 * @return $this
	 */
	public function addDeviationSystem(DeviationSystem $deviationSystem): self
	{
		if (!$this->deviationsSystem->contains($deviationSystem)) {
			$this->deviationsSystem[] = $deviationSystem;
			$deviationSystem->setReferentQA($this);
		}

		return $this;
	}

	/**
	 * @param DeviationSystem $deviationSystem
	 * @return $this
	 */
	public function removeDeviationSystem(DeviationSystem $deviationSystem): self
	{
		if ($this->deviationsSystem->removeElement($deviationSystem)) {
			$deviationSystem->setReferentQA(null);
		}

		return $this;
	}

	/**
	 * @return Collection|DeviationSystem[]
	 */
	public function getDeviationsSystemOfficial(): Collection
	{
		return $this->deviationsSystemOfficial;
	}

	/**
	 * @param DeviationSystem $deviationSystem
	 * @return $this
	 */
	public function addDeviationSystemOfficial(DeviationSystem $deviationSystem): self
	{
		if (!$this->deviationsSystemOfficial->contains($deviationSystem)) {
			$this->deviationsSystemOfficial[] = $deviationSystem;
			$deviationSystem->setOfficialQA($this);
		}

		return $this;
	}

	/**
	 * @param DeviationSystem $deviationSystem
	 * @return $this
	 */
	public function removeDeviationSystemOfficial(DeviationSystem $deviationSystem): self
	{
		if ($this->deviationsSystemOfficial->removeElement($deviationSystem)) {
			$deviationSystem->setOfficialQA(null);
		}

		return $this;
	}

	/**
	 * @return Collection|DeviationSample[]
	 */
	public function getDeviationSamples(): Collection
	{
		return $this->deviationSamples;
	}

	/**
	 * @param DeviationSample $deviationSample
	 * @return $this
	 */
	public function addDeviationSample(DeviationSample $deviationSample): self
	{
		if (!$this->deviationSamples->contains($deviationSample)) {
			$this->deviationSamples[] = $deviationSample;
			$deviationSample->setDeclaredBy($this);
		}

		return $this;
	}

	/**
	 * @param DeviationSample $deviationSample
	 * @return $this
	 */
	public function removeDeviationSample(DeviationSample $deviationSample): self
	{
		if ($this->deviationSamples->removeElement($deviationSample)) {
			$deviationSample->setDeclaredBy(null);
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
	 * @param DeviationAction $action
	 * @return $this
	 */
	public function addDeviationAction(DeviationAction $action): self
	{
		if (!$this->deviationActions->contains($action)) {
			$this->deviationActions[] = $action;
			$action->setIntervener($this);
		}

		return $this;
	}

	/**
	 * @param DeviationAction $action
	 * @return $this
	 */
	public function removeDeviationAction(DeviationAction $action): self
	{
		if ($this->deviationActions->removeElement($action)) {
			// set the owning side to null (unless already changed)
			if ($action->getIntervener() === $this) {
				$action->setIntervener(null);
			}
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
	 * @param DeviationReviewAction $action
	 * @return $this
	 */
	public function addDeviationReviewAction(DeviationReviewAction $action): self
	{
		if (!$this->deviationReviewActions->contains($action)) {
			$this->deviationReviewActions[] = $action;
			$action->setIntervener($this);
		}

		return $this;
	}

	/**
	 * @param DeviationReviewAction $action
	 * @return $this
	 */
	public function removeDeviationReviewAction(DeviationReviewAction $action): self
	{
		if ($this->deviationReviewActions->removeElement($action)) {
			// set the owning side to null (unless already changed)
			if ($action->getIntervener() === $this) {
				$action->setIntervener(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection|DeviationSystemAction[]
	 */
	public function getDeviationSystemActions(): Collection
	{
		return $this->deviationSystemActions;
	}

	/**
	 * @param DeviationSystemAction $action
	 * @return $this
	 */
	public function addDeviationSystemAction(DeviationSystemAction $action): self
	{
		if (!$this->deviationSystemActions->contains($action)) {
			$this->deviationSystemActions[] = $action;
			$action->setUser($this);
		}

		return $this;
	}

	/**
	 * @param DeviationSystemAction $action
	 * @return $this
	 */
	public function removeDeviationSystemAction(DeviationSystemAction $action): self
	{
		if ($this->deviationSystemActions->removeElement($action)) {
			// set the owning side to null (unless already changed)
			if ($action->getUser() === $this) {
				$action->setUser(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection|DeviationSystemReviewAction[]
	 */
	public function getDeviationSystemReviewActions(): Collection
	{
		return $this->deviationSystemReviewActions;
	}

	/**
	 * @param DeviationSystemReviewAction $action
	 * @return $this
	 */
	public function addDeviationSystemReviewAction(DeviationSystemReviewAction $action): self
	{
		if (!$this->deviationSystemReviewActions->contains($action)) {
			$this->deviationSystemReviewActions[] = $action;
			$action->setIntervener($this);
		}

		return $this;
	}

	/**
	 * @param DeviationSystemReviewAction $action
	 * @return $this
	 */
	public function removeDeviationSystemReviewAction(DeviationSystemReviewAction $action): self
	{
		if ($this->deviationSystemReviewActions->removeElement($action)) {
			// set the owning side to null (unless already changed)
			if ($action->getIntervener() === $this) {
				$action->setIntervener(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection|DeviationSampleAction[]
	 */
	public function getDeviationSampleActions(): Collection
	{
		return $this->deviationSampleActions;
	}

	/**
	 * @param DeviationSampleAction $deviationSampleAction
	 * @return $this
	 */
	public function addDeviationSampleAction(DeviationSampleAction $deviationSampleAction): self
	{
		if (!$this->deviationActions->contains($deviationSampleAction)) {
			$this->deviationActions[] = $deviationSampleAction;
			$deviationSampleAction->setUser($this);
		}

		return $this;
	}

	/**
	 * @param DeviationSampleAction $deviationSampleAction
	 * @return $this
	 */
	public function removeDeviationSampleAction(DeviationSampleAction $deviationSampleAction): self
	{
		if ($this->deviationActions->removeElement($deviationSampleAction)) {
			// set the owning side to null (unless already changed)
			if ($deviationSampleAction->getUser() === $this) {
				$deviationSampleAction->setUser(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection|DeviationReview[]
	 */
	public function getDeviationReviews(): Collection
	{
		return $this->deviationReviews;
	}

	/**
	 * @param DeviationReview $review
	 * @return $this
	 */
	public function addDeviationReview(DeviationReview $review): self
	{
		if (!$this->deviationReviews->contains($review)) {
			$this->deviationReviews[] = $review;
			$review->setReader($this);
		}

		return $this;
	}

	/**
	 * @param DeviationReview $review
	 * @return $this
	 */
	public function removeDeviationReview(DeviationReview $review): self
	{
		if ($this->deviationReviews->removeElement($review)) {
			// set the owning side to null (unless already changed)
			if ($review->getReader() === $this) {
				$review->setReader(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection|ReportConfigVersion[]
	 */
	public function getReportConfigVersions(): Collection
	{
		return $this->reportConfigVersions;
	}

	/**
	 * @param ReportConfigVersion $reportConfigVersion
	 * @return $this
	 */
	public function addReportConfigVersion(ReportConfigVersion $reportConfigVersion): self
	{
		if (!$this->reportConfigVersions->contains($reportConfigVersion)) {
			$this->reportConfigVersions[] = $reportConfigVersion;
			$reportConfigVersion->setConfiguredBy($this);
		}

		return $this;
	}

	/**
	 * @param ReportConfigVersion $reportConfigVersion
	 * @return $this
	 */
	public function removeReportConfigVersion(ReportConfigVersion $reportConfigVersion): self
	{
		if ($this->reportConfigVersions->removeElement($reportConfigVersion)) {
			// set the owning side to null (unless already changed)
			if ($reportConfigVersion->getConfiguredBy() === $this) {
				$reportConfigVersion->setConfiguredBy(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection|Mailgroup[]
	 */
	public function getMailgroup(): Collection
	{
		return $this->mailgroups;
	}

	/**
	 * @param Mailgroup $mailgroup
	 * @return $this
	 */
	public function addMailgroup(Mailgroup $mailgroup): self
	{
		if (!$this->mailgroups->contains($mailgroup)) {
			$this->mailgroups[] = $mailgroup;
			$mailgroup->addUser($this);
		}

		return $this;
	}

	/**
	 * @param Mailgroup $mailgroup
	 * @return $this
	 */
	public function removeMailgroup(Mailgroup $mailgroup): self
	{
		if ($this->mailgroups->removeElement($mailgroup)) {
			// set the owning side to null (unless already changed)
			if ($mailgroup->getUsers()->contains($mailgroup)) {
				$mailgroup->removeUser($this);
			}
		}

		return $this;
	}

    /**
     * @return ArrayCollection<int, DocumentVersion>
     */
	public function getDocumentVersionsValidatedQaBy(): Collection
	{
		return $this->documentVersionsValidatedQaBy;
	}

	/**
	 * @param DocumentVersion $documentVersion
	 * @return $this
	 */
	public function addDocumentVersionValidatedQaBy(DocumentVersion $documentVersion): self
	{
		if (!$this->documentVersionsValidatedQaBy->contains($documentVersion)) {
			$this->documentVersionsValidatedQaBy[] = $documentVersion;
            $documentVersion->setValidatedQaBy($this);
		}

		return $this;
	}

	/**
	 * @param DocumentVersion $documentVersion
	 * @return $this
	 */
	public function removeDocumentVersionValidatedQaBy(DocumentVersion $documentVersion): self
	{
		if ($this->documentVersionsValidatedQaBy->removeElement($documentVersion)) {
			// set the owning side to null (unless already changed)
			if ($documentVersion->getValidatedQaBy() === $this) {
                $documentVersion->setValidatedQaBy(null);
			}
		}

		return $this;
	}

    /**
     * @return ArrayCollection<int, DocumentVersion>
     */
    public function getDocumentVersionAuthor(): Collection
    {
        return $this->documentVersionsAuthor;
    }

    /**
     * @param DocumentVersion $documentVersion
     * @return $this
     */
    public function addDocumentVersionsAuthor(DocumentVersion $documentVersion): self
    {
        if (!$this->documentVersionsAuthor->contains($documentVersion)) {
            $this->documentVersionsAuthor[] = $documentVersion;
            $documentVersion->setAuthor($this);
        }

        return $this;
    }

    /**
     * @param DocumentVersion $documentVersion
     * @return $this
     */
    public function removeDocumentVersionsAuthor(DocumentVersion $documentVersion): self
    {
        if ($this->documentVersionsAuthor->removeElement($documentVersion)) {
            // set the owning side to null (unless already changed)
            if ($documentVersion->getAuthor() === $this) {
                $documentVersion->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection<int, DocumentVersion>
     */
    public function getDocumentVersionsCreatedBy(): Collection
    {
        return $this->documentVersionsCreatedBy;
    }

    /**
     * @param DocumentVersion $documentVersion
     * @return $this
     */
    public function addDocumentVersionCreatedBy(DocumentVersion $documentVersion): self
    {
        if (!$this->documentVersionsCreatedBy->contains($documentVersion)) {
            $this->documentVersionsCreatedBy[] = $documentVersion;
            $documentVersion->getCreatedBy($this);
        }

        return $this;
    }

    /**
     * @param DocumentVersion $documentVersion
     * @return $this
     */
    public function removeDocumentVersionCreatedBy(DocumentVersion $documentVersion): self
    {
        if ($this->documentVersionsAuthor->removeElement($documentVersion)) {
            // set the owning side to null (unless already changed)
            if ($documentVersion->getCreatedBy() === $this) {
                $documentVersion->setCreatedBy(null);
            }
        }

        return $this;
    }
}
