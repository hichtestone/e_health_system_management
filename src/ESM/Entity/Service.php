<?php

namespace App\ESM\Entity;

use App\ESM\Entity\DropdownList\Country;
use App\ESM\Repository\ServiceRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ServiceRepository::class)
 * @UniqueEntity(
 *     fields={"institution", "name"},
 *     message="Ce Numéro de centre {{ value }} est déjà utilisé."
 *  )
 */
class Service
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     *
     * @var string
     */
    private $name;

	/**
	 * @ORM\Column(type="string", length=255, nullable=false)
	 *
	 * @var string
	 */
	private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address2;

    /**
     * @ORM\Column(type="boolean")
     */
    private $addressInherited = true;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $postalCode;

    /**
     * @ORM\ManyToOne(targetEntity=Institution::class, inversedBy="services")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Institution
     */
    private $institution;

    /**
     * @ORM\OneToMany (targetEntity=InterlocutorCenter::class, mappedBy="service")
     *
     * @var ArrayCollection<int, InterlocutorCenter>
     */
    private $interlocutorCenters;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $deletedAt;

	/**
	 *
	 */
    public function __construct()
    {
        $this->interlocutorCenters = new ArrayCollection();
    }

	/**
	 * @return int|null
	 */
    public function getId(): ?int
    {
        return $this->id;
    }

	/**
	 * @return string[]
	 */
    public function getFieldsToBeIgnored(): array
    {
        return ['institution', 'interlocutorCenters'];
    }

	/**
	 * @return string
	 */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

	/**
	 * @param string $name
	 */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

	/**
	 * @return string
	 */
	public function getSlug(): string
	{
		return $this->slug;
	}

	/**
	 * @param string $slug
	 */
	public function setSlug(string $slug): void
	{
		$this->slug = $slug;
	}

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address): void
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @param mixed $address2
     */
    public function setAddress2($address2): void
    {
        $this->address2 = $address2;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

	/**
	 * @return Country|null
	 */
    public function getCountry(): ?Country
    {
        return null === $this->institution ? null : $this->institution->getCountry();
    }

    /**
     * @return mixed
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param mixed $postalCode
     */
    public function setPostalCode($postalCode): void
    {
        $this->postalCode = $postalCode;
    }

	/**
	 * @return Institution
	 */
    public function getInstitution(): Institution
    {
        return $this->institution;
    }

	/**
	 * @param Institution $institution
	 */
    public function setInstitution(Institution $institution): void
    {
        $this->institution = $institution;
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
	 */
    public function setDeletedAt(?DateTime $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

	/**
	 * @return bool
	 */
    public function isAddressInherited(): bool
    {
        return $this->addressInherited;
    }

	/**
	 *
	 */
    public function resetAddress(): void
    {
        $this->address = null;
        $this->address2 = null;
        $this->city = null;
        $this->postalCode = null;
    }

	/**
	 * @param bool $addressInherited
	 */
    public function setAddressInherited(bool $addressInherited): void
    {
        $this->addressInherited = $addressInherited;
    }

    /**
     * @return ArrayCollection<int, InterlocutorCenter>
     */
    public function getInterlocutorCenters(): Collection
    {
        return $this->interlocutorCenters;
    }
}
