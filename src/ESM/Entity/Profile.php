<?php

namespace App\ESM\Entity;

use App\ESM\Repository\ProfileRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ProfileRepository::class)
 * @UniqueEntity(fields={"name"})
 * @UniqueEntity(fields={"acronyme"})
 */
class Profile implements AuditrailableInterface
{
	public const PROFIL_ADMIN_ACRONYM                      = 'ADM';
	public const PROFIL_ATTACHED_RESEARCH_CLINICAL_ACRONYM = 'ARC';
	public const PROFIL_CHIEF_OF_PROJECT_ACRONYM           = 'CP';
	public const PROFIL_QUALITY_ASSURANCE_ACRONYM          = 'QA';
	public const PROFIL_DATA_MANAGER_ACRONYM               = 'DM';
	public const PROFIL_OPERATIONAL_DIRECTOR_ACRONYM       = 'Dir OP';
	public const PROFIL_INVESTIGATOR_ACRONYM               = 'INV';

	public const PROFIL_ADMIN_LABEL                      = 'admin';
	public const PROFIL_ATTACHED_RESEARCH_CLINICAL_LABEL = 'attaché de recherche clinique';
	public const PROFIL_CHIEF_OF_PROJECT_LABEL           = 'chef de projet';
	public const PROFIL_QUALITY_ASSURANCE_LABEL          = 'Assurance qualité';
	public const PROFIL_DATA_MANAGER_LABEL               = 'data manager';
	public const PROFIL_OPERATIONAL_DIRECTOR_LABEL       = 'directeur des opérations';
	public const PROFIL_INVESTIGATOR_LABEL               = 'investigateur';

	public const LIST_PROFILES = [
		self::PROFIL_ADMIN_ACRONYM 						=> self::PROFIL_ADMIN_LABEL,
		self::PROFIL_ATTACHED_RESEARCH_CLINICAL_ACRONYM => self::PROFIL_ATTACHED_RESEARCH_CLINICAL_LABEL,
		self::PROFIL_CHIEF_OF_PROJECT_ACRONYM 			=> self::PROFIL_CHIEF_OF_PROJECT_LABEL,
		self::PROFIL_QUALITY_ASSURANCE_ACRONYM 			=> self::PROFIL_QUALITY_ASSURANCE_LABEL,
		self::PROFIL_DATA_MANAGER_ACRONYM 				=> self::PROFIL_DATA_MANAGER_LABEL,
		self::PROFIL_OPERATIONAL_DIRECTOR_ACRONYM 		=> self::PROFIL_OPERATIONAL_DIRECTOR_LABEL,
		self::PROFIL_INVESTIGATOR_ACRONYM 				=> self::PROFIL_INVESTIGATOR_LABEL
	];

	public const PROFIL_TYPE_ESM 	= 'ESM';
	public const PROFIL_TYPE_ETMF 	= 'ETMF';

	public const PROFILS_TYPE = [
		self::PROFIL_TYPE_ESM 	=> 'ESM',
		self::PROFIL_TYPE_ETMF 	=> 'ETMF'
	];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

	/**
	 * @ORM\Column(type="string", length=255)
	 * @var string
	 */
	private $name;

	/**
	 * @ORM\Column(type="string", length=25)
	 * @var string
	 */
	private $acronyme;

	/**
	 * @ORM\Column(type="string", length=10)
	 * @var string
	 */
	private $type;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * @var DateTime|null
	 */
	private $deletedAt;

	/**
     * @ORM\ManyToMany(targetEntity=Role::class, inversedBy="profiles")
     * @var ArrayCollection<int, Role>
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="profile")
     * @var ArrayCollection<int, User>
     */
    private $users;

	/**
	 * constructor
	 */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

	/**
	 * @return string
	 */
    public function __toString()
    {
        return $this->name;
    }

	/**
	 * @return int|null
	 */
    public function getId(): ?int
    {
        return $this->id;
    }

	/**
	 * @return string|null
	 */
	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return $this
	 */
	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getAcronyme(): ?string
	{
		return $this->acronyme;
	}

	/**
	 * @param string $acronyme
	 * @return $this
	 */
	public function setAcronyme(string $acronyme): self
	{
		$this->acronyme = $acronyme;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getType(): ?string
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 * @throws Exception
	 */
	public function setType(string $type): void
	{
		if (!array_key_exists($type, self::PROFILS_TYPE)) {
			throw new Exception('The profile type : ' . $type . 'is not authorize !');
		}

		$this->type = $type;
	}

	/**
	 * @return string[]
	 */
	public function getFieldsToBeIgnored(): array
    {
        return ['users'];
    }

    /**
     * @return ArrayCollection<int, Role>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

	/**
	 * @param Role $role
	 * @return $this
	 */
    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

	/**
	 * @param Role $role
	 * @return $this
	 */
    public function removeRole(Role $role): self
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
        }

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
     * @return ArrayCollection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

	/**
	 * @return array
	 */
    public function getSecurity(): array
    {
        $roles = [];
        foreach ($this->roles as $role) {
            $roles[] = $role->getCode();
        }

        return array_unique($roles);
    }

	/**
	 * @param User $user
	 * @return $this
	 */
    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setProfile($this);
        }

        return $this;
    }

	/**
	 * @param User $user
	 * @return $this
	 */
    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getProfile() === $this) {
                $user->setProfile(null);
            }
        }

        return $this;
    }
}
