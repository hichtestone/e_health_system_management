<?php

namespace App\ESM\Entity;

use App\ESM\Entity\DropdownList\Country;
use App\ESM\Entity\DropdownList\CountryDepartment;
use App\ESM\Entity\DropdownList\InstitutionType;
use App\ESM\Repository\InstitutionRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=InstitutionRepository::class)
 * @UniqueEntity(
 *     fields={"name", "city"},
 *  )
 */
class Institution implements AuditrailableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="entity.Institution.field.notBlank.name")
     */
    private $name;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\ManyToOne(targetEntity=Country::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Country
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=9)
     * @Assert\Length(min=9, max=9, exactMessage="Le N°FINESS doit comporter 9 chiffres")
     */
    private $finess;

	/**
	 * @ORM\Column(type="string", length=9, nullable=true)
	 * @Assert\Length(min=9, max=9, exactMessage="Le N°FINESS_TMP doit comporter 9 chiffres")
	 */
	private $finessTmp;

    /**
     * @ORM\Column(type="string", length=14, nullable=true)
     * @Assert\Length(min=14, max=14, exactMessage="Le N°SIRET doit comporter 14 chiffres")
	 *
	 * @var string|null
     */
    private $siret;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $fax;

    /**
     * @ORM\Column(type="string", length=55, nullable=true)
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity=CountryDepartment::class)
     *
     * @var CountryDepartment
     */
    private $countryDepartment;

    /**
     * @ORM\ManyToOne(targetEntity=InstitutionType::class)
	 * @ORM\JoinColumn(nullable=false)
     * @var InstitutionType
     */
    private $institutionType;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $deletedAt;

    /**
     * @ORM\OneToMany(targetEntity=Service::class, mappedBy="institution")
     * @ORM\OrderBy({"name" = "ASC"})
     *
     * @var ArrayCollection<int, Service>
     */
    private $services;

    /**
     * @ORM\ManyToMany(targetEntity=Interlocutor::class, mappedBy="institutions")
     * @ORM\OrderBy({"lastName" = "ASC"})
     *
     * @var ArrayCollection<int, Interlocutor>
     */
    private $interlocutors;

    /**
     * @ORM\ManyToMany(targetEntity=Center::class, mappedBy="institutions")
     * @ORM\JoinTable(name="institution_center")
     *
     * @var ArrayCollection<int, Center>
     */
    private $centers;

    /**
     * @ORM\OneToMany(targetEntity=DocumentTransverse::class, mappedBy="institution")
     */
    private $documentTransverses;

    /**
     * @ORM\OneToMany (targetEntity=Deviation::class, mappedBy="institution")
     *
     * @var ArrayCollection<int, Deviation>
     */
    private $deviations;

	/**
	 * @ORM\ManyToMany(targetEntity=DeviationSample::class, mappedBy="institutions")
	 * @ORM\JoinTable(name="deviation_sample_institution")
	 *
	 * @var ArrayCollection<int, DeviationSample>
	 */
	private $deviationSamples;

    public function __construct()
    {
        $this->services 			= new ArrayCollection();
        $this->interlocutors 		= new ArrayCollection();
        $this->centers 				= new ArrayCollection();
        $this->documentTransverses 	= new ArrayCollection();
        $this->deviations 			= new ArrayCollection();
        $this->deviationSamples 	= new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFieldsToBeIgnored(): array
    {
        return [];
    }

    public function __toString(): ?string
    {
        return $this->name ?? '';
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

	/**
	 * @return mixed
	 */
	public function getSlug()
	{
		return $this->slug;
	}

	/**
	 * @param mixed $slug
	 */
	public function setSlug($slug): void
	{
		$this->slug = $slug;
	}

    public function getAddress1(): ?string
    {
        return $this->address1;
    }

    public function setAddress1(string $address1): self
    {
        $this->address1 = $address1;

        return $this;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

	/**
	 * @return string|null
	 */
    public function getFiness(): ?string
    {
        return $this->finess;
    }

	/**
	 * @param string $finess
	 * @return $this
	 */
    public function setFiness(string $finess): self
    {
        $this->finess = $finess;

        return $this;
    }

	/**
	 * @return string|null
	 */
	public function getFinessTmp(): ?string
	{
		return $this->finessTmp;
	}

	/**
	 * @param string $finessTmp
	 * @return $this
	 */
	public function setFinessTmp(string $finessTmp): self
	{
		$this->finessTmp = $finessTmp;

		return $this;
	}

	/**
	 * @return string|null
	 */
    public function getSiret(): ?string
    {
        return $this->siret;
    }

	/**
	 * @param string|null $siret
	 * @return $this
	 */
    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCountryDepartment(): ?CountryDepartment
    {
        return $this->countryDepartment;
    }

    public function setCountryDepartment(?CountryDepartment $department): void
    {
        $this->countryDepartment = $department;
    }

    public function getInstitutionType(): ?InstitutionType
    {
        return $this->institutionType;
    }

    public function setInstitutionType(?InstitutionType $institutionType): void
    {
        $this->institutionType = $institutionType;
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

    /**
     * @return ArrayCollection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    /**
     * @return ArrayCollection<int, Center>
     */
    public function getCenters(): Collection
    {
        return $this->centers;
    }

    /**
     * @return ArrayCollection<int, Interlocutor>
     */
    public function getInterlocutors(): Collection
    {
        return $this->interlocutors;
    }

    /**
     * @return Collection|DocumentTransverse[]
     */
    public function getDocumentTransverses(): Collection
    {
        return $this->documentTransverses;
    }

    public function addDocumentTransverse(DocumentTransverse $documentTransverse): self
    {
        if (!$this->documentTransverses->contains($documentTransverse)) {
            $this->documentTransverses[] = $documentTransverse;
            $documentTransverse->addInstitution($this);
        }

        return $this;
    }

    public function removeDocumentTransverse(DocumentTransverse $documentTransverse): self
    {
        if ($this->documentTransverses->removeElement($documentTransverse)) {
            $documentTransverse->removeInstitution($this);
        }

        return $this;
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
            $deviation->setInstitution($this);
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
            // set the owning side to null (unless already changed)
            if ($deviation->getInstitution() === $this) {
                $deviation->setInstitution(null);
            }
        }

        return $this;
    }

	/**
	 * @return Collection
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
		}

		return $this;
	}

	/**
	 * @param DeviationSample $deviationSample
	 * @return $this
	 */
	public function removeDeviationSample(DeviationSample $deviationSample): self
	{
		if ($this->deviationSamples->contains($deviationSample)) {
			$this->deviationSamples->removeElement($deviationSample);
		}

		return $this;
	}
}
