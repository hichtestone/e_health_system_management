<?php

namespace App\ESM\Entity;

use App\ESM\Entity\DropdownList\Country;
use App\ESM\Entity\DropdownList\CrfType;
use App\ESM\Entity\DropdownList\MembershipGroup;
use App\ESM\Entity\DropdownList\PatientNumber;
use App\ESM\Entity\DropdownList\PaymentUnit;
use App\ESM\Entity\DropdownList\ProjectStatus;
use App\ESM\Entity\DropdownList\Sponsor;
use App\ESM\Entity\DropdownList\Territory;
use App\ESM\Entity\DropdownList\TrailPhase;
use App\ESM\Entity\DropdownList\TrailType;
use App\ESM\Entity\DropdownList\TrlIndice;
use App\ESM\Repository\ProjectRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use App\ETMF\Entity\Document;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 * @UniqueEntity(fields={"name"})
 * @UniqueEntity(fields={"acronyme"})
 * @UniqueEntity(fields={"nameEnglish"})
 * @Vich\Uploadable
 */
class Project implements AuditrailableInterface
{
	public const STUDY_POPULATION_PEDIATRIC   = 0;
	public const STUDY_POPULATION_ADULT  = 1;

	public const STUDY_POPULATION = [
		self::STUDY_POPULATION_PEDIATRIC   => 'Pédiatrique',
		self::STUDY_POPULATION_ADULT  => 'Adulte',
	];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     * @Groups({"project"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=55)
     *
     * @var string
     */
    private $appToken;

    /**
     * @ORM\Column(type="text", nullable=false)
     *
     * @var string
     * @Groups({"project"})
     */
    private $name;

	/**
	 * @ORM\Column(type="text", nullable=false, length=65000)
     * @Groups({"project"})
	 */
	private $nameEnglish;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $protocole;

    /**
     * @ORM\ManyToOne(targetEntity=Sponsor::class, inversedBy="projects")
     *
     * @var Sponsor
     */
    private $sponsor;

    /**
     * @ORM\ManyToMany(targetEntity=Country::class, inversedBy="projects")
     *
     * @var ArrayCollection<int, Country>
     */
    private $countries;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=true)
     *
     * @var User
     */
    private $responsiblePM;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=true)
     *
     * @var User
     */
    private $responsibleCRA;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @var bool
     */
    private $hasETMF = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $crfUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $etmfUrl;

    /**
     * @ORM\Column(type="string", length=55, nullable=true)
     *
     * @var string
     */
    private $ref;

    /**
     * @ORM\ManyToOne(targetEntity=CrfType::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var CrfType
     */
    private $crfType;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $closeDemandedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=true)
     *
     * @var User|null
     */
    private $closedDemandBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $closedAt;

    /**
     * @Assert\Image(
     *     mimeTypes= "image/*"
     * )
     * @Vich\UploadableField(mapping="project_image", fileNameProperty="logo")
     *
     * @var File|null
     */
    private $logoFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @ORM\Column(type="string", length=7)
     * @Assert\Regex(
     *     pattern="/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/",
     *     message="Votre backroundColor ne respecte pas le format hexadeximale"
     * )
     * @var string
     */
    private $fontColor = '#2c4d7a';

    /**
     * @ORM\Column(type="string", length=7)
     * @Assert\Regex(
     *     pattern="/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/",
     *     message="Votre backroundColor ne respecte pas le format hexadeximale"
     * )
     * @var string
     */
    private $backgroundColor = '#ffffff';

    /**
     * @ORM\OneToMany(targetEntity=Center::class, mappedBy="project", cascade={"persist"})
     * @var ArrayCollection<int, Center>
     */
    private $centers;

    /**
     * @ORM\OneToMany(targetEntity=UserProject::class, mappedBy="project")
     * @var ArrayCollection<int, UserProject>
     */
    private $userProjects;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=ProjectStatus::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $projectStatus;

	/**
	 * @ORM\Column(type="json")
	 * @var array
	 */
	private $studyPopulation = [];

    /**
     * @ORM\ManyToOne(targetEntity=TrailType::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $trailType;

    /**
     * @ORM\ManyToOne(targetEntity=TrailPhase::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $trailPhase;

    /**
     * @ORM\ManyToOne(targetEntity=PatientNumber::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $patientNumber;

    /**
     * @ORM\ManyToOne(targetEntity=TrlIndice::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $trlIndice;

    /**
     * @ORM\ManyToOne(targetEntity=Territory::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $territory;

    /**
     * @ORM\ManyToOne(targetEntity=MembershipGroup::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $membershipGroup;

    /**
     * @ORM\ManyToOne(targetEntity=PaymentUnit::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $paymentUnit;

    /**
     * @ORM\ManyToOne(targetEntity=Delegation::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     *
     * @var Delegation|null
     */
    private $delegation;

    /**
     * @ORM\OneToMany (targetEntity=Deviation::class, mappedBy="project")
     *
     * @var ArrayCollection<int, Deviation>
     */
    private $deviations;

    /**
     * @ORM\OneToMany (targetEntity=DeviationCorrection::class, mappedBy="project")
     *
     * @var ArrayCollection<int, DeviationCorrection>
     */
    private $deviationCorrections;

    /**
     * @ORM\Column(type="string", length=30, unique=true)
     *
     * @Groups({"project"})
     */
    private $acronyme;

    /**
     * @ORM\Column(type="string", length=55, nullable=true)
     */
    private $nctNumber;

    /**
     * @ORM\Column(type="string", length=55, nullable=true)
     */
    private $eudractNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coordinatingInvestigators;

    /**
     * @ORM\Column(type="json")
     *
     * @var array
     */
    private $metasParticipant = [];

    /**
     * @ORM\Column(type="json")
     *
     * @var array
     */
    private $metasUser = [];

    /**
     * @ORM\ManyToMany(targetEntity=Drug::class, mappedBy="projects")
     * @ORM\JoinTable(name="project_drug")
     */
    private $drugs;

    /**
     * @ORM\OneToOne(targetEntity=CourbeSetting::class, cascade={"persist", "remove"})
     */
    private $courbe;

    /**
     * @ORM\OneToMany(targetEntity=ProjectTrailTreatment::class, mappedBy="project")
     */
    private $projectTrailTreatments;

    /**
     * @ORM\OneToMany (targetEntity=ReportVisit::class, mappedBy="project")
     *
     * @var ArrayCollection<int, ReportVisit>
     */
    private $reportVisits;

    /**
     * @ORM\OneToMany (targetEntity=ReportConfig::class, mappedBy="project")
     *
     * @var ArrayCollection<int, ReportConfig>
     */
    private $reportConfigs;

	/**
	 * @ORM\ManyToMany(targetEntity=DeviationSample::class, mappedBy="projects")
	 * @ORM\JoinTable(name="deviation_sample_project")
	 *
	 * @var ArrayCollection<int, DeviationSample>
	 */
	private $deviationSamples;

    /**
     * @ORM\OneToMany(targetEntity=Document::class, mappedBy="project")
     * @ORM\JoinColumn(nullable=false)
     * @var ArrayCollection<int, Document>
     */
    private $documents;


    public function __construct()
    {
        $this->countries 				= new ArrayCollection();
        $this->trailTreatments 			= new ArrayCollection();
        $this->centers 					= new ArrayCollection();
        $this->userProjects 			= new ArrayCollection();
        $this->drugs 					= new ArrayCollection();
        $this->courbeSettings 			= new ArrayCollection();
        $this->projectTrailTreatments 	= new ArrayCollection();
        $this->deviations 				= new ArrayCollection();
        $this->reportVisits 			= new ArrayCollection();
        $this->reportConfigs 			= new ArrayCollection();
        $this->deviationSamples 		= new ArrayCollection();
        $this->documents 				= new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFieldsToBeIgnored(): array
    {
        return ['createdAt', 'appToken', 'centers', 'userProjects', 'updatedAt', 'metasUser', 'metasParticipant', 'courbe'];
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getProtocole(): ?string
    {
        return $this->protocole;
    }

    public function setProtocole(string $protocole): void
    {
        $this->protocole = $protocole;
    }

    public function getSponsor(): ?Sponsor
    {
        return $this->sponsor;
    }

    public function setSponsor(Sponsor $sponsor): void
    {
        $this->sponsor = $sponsor;
    }

    /**
     * @return ArrayCollection<int, Country>
     */
    public function getCountries(): Collection
    {
        return $this->countries;
    }

	/**
	 * @param Country $country
	 * @return $this
	 */
    public function addCountry(Country $country): self
    {
        if (!$this->countries->contains($country)) {
            $this->countries[] = $country;
        }

        return $this;
    }

	/**
	 * @param Country $country
	 * @return $this
	 */
    public function removeCountry(Country $country): self
    {
        if ($this->countries->contains($country)) {
            $this->countries->removeElement($country);
        }

        return $this;
    }

	/**
	 * @param Country $country
	 * @return bool
	 */
    public function hasCountry(Country $country): bool
    {
        return $this->countries->contains($country);
    }

    public function isHasETMF(): bool
    {
        return $this->hasETMF;
    }

    public function setHasETMF(bool $hasETMF): void
    {
        $this->hasETMF = $hasETMF;
    }

    public function getCrfUrl(): ?string
    {
        return $this->crfUrl;
    }

    public function setCrfUrl(?string $crfUrl): void
    {
        $this->crfUrl = $crfUrl;
    }

    public function getEtmfUrl(): ?string
    {
        return $this->etmfUrl;
    }

    public function setEtmfUrl(string $etmfUrl): void
    {
        $this->etmfUrl = $etmfUrl;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(string $ref): void
    {
        $this->ref = $ref;
    }

    public function getCrfType(): ?CrfType
    {
        return $this->crfType;
    }

    public function setCrfType(?CrfType $crfType): void
    {
        $this->crfType = $crfType;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCloseDemandedAt(): ?DateTime
    {
        return $this->closeDemandedAt;
    }

    public function setCloseDemandedAt(?DateTime $closeDemandedAt): void
    {
        $this->closeDemandedAt = $closeDemandedAt;
    }

    public function getClosedAt(): ?DateTime
    {
        return $this->closedAt;
    }

    public function setClosedAt(?DateTime $closedAt): void
    {
        $this->closedAt = $closedAt;
    }

    public function getResponsiblePM(): ?User
    {
        return $this->responsiblePM;
    }

    public function setResponsiblePM(User $responsiblePM): void
    {
        $this->responsiblePM = $responsiblePM;
    }

    public function getResponsibleCRA(): ?User
    {
        return $this->responsibleCRA;
    }

    public function setResponsibleCRA(User $responsibleCRA): void
    {
        $this->responsibleCRA = $responsibleCRA;
    }

    public function getAppToken(): ?string
    {
        return $this->appToken;
    }

    public function setAppToken(string $appToken): void
    {
        $this->appToken = $appToken;
    }

    /**
     * @return ArrayCollection<int, Center>
     */
    public function getCenters(): Collection
    {
        return $this->centers;
    }

    public function addCenter(Center $center): self
    {
        if (!$this->centers->contains($center)) {
            $this->centers[] = $center;
            $center->setProject($this);
        }

        return $this;
    }

    public function removeCenter(Center $center): self
    {
        if ($this->centers->contains($center)) {
            $this->centers->removeElement($center);
        }

        return $this;
    }

    /**
     * @return ArrayCollection<int, UserProject>
     */
    public function getUserProjects(): Collection
    {
        return $this->userProjects;
    }

    public function addUserProject(UserProject $userProjects): self
    {
        if (!$this->userProjects->contains($userProjects)) {
            $this->userProjects[] = $userProjects;
        }

        return $this;
    }

    public function removeUserProject(UserProject $userProjects): self
    {
        if ($this->userProjects->contains($userProjects)) {
            $this->userProjects->removeElement($userProjects);
        }

        return $this;
    }

    public function getClosedDemandBy(): ?User
    {
        return $this->closedDemandBy;
    }

    public function setClosedDemandBy(?User $closedDemandBy): void
    {
        $this->closedDemandBy = $closedDemandBy;
    }

    public function getHasETMF(): ?bool
    {
        return $this->hasETMF;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo($logo): void
    {
        $this->logo = $logo;
    }

    public function getFontColor(): ?string
    {
        return $this->fontColor;
    }

    public function setFontColor(string $fontColor): void
    {
        $this->fontColor = $fontColor;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(string $backgroundColor): void
    {
        $this->backgroundColor = $backgroundColor;
    }

    public function getLogoFile(): ?File
    {
        return $this->logoFile;
    }

    public function setLogoFile(?File $logoFile): Project
    {
        $this->logoFile = $logoFile;
        if ($this->logoFile instanceof UploadedFile) {
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

    public function getProjectStatus(): ?ProjectStatus
    {
        return $this->projectStatus;
    }

    public function setProjectStatus(?ProjectStatus $projectStatus): void
    {
        $this->projectStatus = $projectStatus;
    }

	/**
	 * @return array
	 */
	public function getStudyPopulation(): array
	{
		return $this->studyPopulation;
	}

	/**
	 * @param array $studyPopulation
	 */
	public function setStudyPopulation(array $studyPopulation): void
	{
		$this->studyPopulation = $studyPopulation;
	}

    public function getTrlIndice(): ?TrlIndice
    {
        return $this->trlIndice;
    }

    public function setTrlIndice(?TrlIndice $trlIndice): void
    {
        $this->trlIndice = $trlIndice;
    }

    public function getAcronyme(): ?string
    {
        return $this->acronyme;
    }

    public function setAcronyme(string $acronyme): self
    {
        $this->acronyme = $acronyme;

        return $this;
    }

    public function getNameEnglish(): ?string
    {
        return $this->nameEnglish;
    }

    public function setNameEnglish(string $nameEnglish): self
    {
        $this->nameEnglish = $nameEnglish;

        return $this;
    }

    public function getTerritory(): ?Territory
    {
        return $this->territory;
    }

    public function setTerritory(Territory $territory): void
    {
        $this->territory = $territory;
    }

    public function getTrailType(): ?TrailType
    {
        return $this->trailType;
    }

    public function setTrailType(TrailType $trailType): void
    {
        $this->trailType = $trailType;
    }

    public function getPatientNumber(): ?PatientNumber
    {
        return $this->patientNumber;
    }

    public function setPatientNumber(?PatientNumber $patientNumber): void
    {
        $this->patientNumber = $patientNumber;
    }

    public function getMembershipGroup(): ?MembershipGroup
    {
        return $this->membershipGroup;
    }

    public function setMembershipGroup(?MembershipGroup $membershipGroup): void
    {
        $this->membershipGroup = $membershipGroup;
    }

    public function getTrailPhase(): ?TrailPhase
    {
        return $this->trailPhase;
    }

    public function setTrailPhase(TrailPhase $trailPhase): void
    {
        $this->trailPhase = $trailPhase;
    }

    public function getPaymentUnit(): ?PaymentUnit
    {
        return $this->paymentUnit;
    }

    public function setPaymentUnit(?PaymentUnit $paymentUnit): void
    {
        $this->paymentUnit = $paymentUnit;
    }

    public function getDelegation(): ?Delegation
    {
        return $this->delegation;
    }

    public function setDelegation(?Delegation $delegation): void
    {
        $this->delegation = $delegation;
    }

    public function getNctNumber(): ?string
    {
        return $this->nctNumber;
    }

    public function setNctNumber(?string $nctNumber): self
    {
        $this->nctNumber = $nctNumber;

        return $this;
    }

    public function getEudractNumber(): ?string
    {
        return $this->eudractNumber;
    }

    public function setEudractNumber(?string $eudractNumber): self
    {
        $this->eudractNumber = $eudractNumber;

        return $this;
    }

    public function getCoordinatingInvestigators(): ?string
    {
        return $this->coordinatingInvestigators;
    }

    public function setCoordinatingInvestigators(?string $coordinatingInvestigators): self
    {
        $this->coordinatingInvestigators = $coordinatingInvestigators;

        return $this;
    }

    public function getMetasParticipant(): array
    {
        return $this->metasParticipant;
    }

    public function setMetasParticipant(array $metasParticipant): void
    {
        $this->metasParticipant = $metasParticipant;
    }

    public function getMetasUser(): array
    {
        return $this->metasUser;
    }

    public function setMetasUser(array $metasUser): void
    {
        $this->metasUser = $metasUser;
    }

    public function hasUser(User $user): bool
    {
        foreach ($this->getUserProjects() as $userProject) {
            if (null === $userProject->getDisabledAt()
                && $userProject->getUser()->getId() === $user->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Collection|Drug[]
     */
    public function getDrugs(): Collection
    {
        return $this->drugs;
    }

    public function addDrug(Drug $drug): self
    {
        if (!$this->drugs->contains($drug)) {
            $this->drugs[] = $drug;
            $drug->addProject($this);
        }

        return $this;
    }

    public function removeDrug(Drug $drug): self
    {
        if ($this->drugs->removeElement($drug)) {
            $drug->removeProject($this);
        }

        return $this;
    }

    public function getCourbe(): ?CourbeSetting
    {
        return $this->courbe;
    }

    public function setCourbe(?CourbeSetting $courbe): self
    {
        $this->courbe = $courbe;

        return $this;
    }

    /**
     * @return Collection|ProjectTrailTreatment[]
     */
    public function getProjectTrailTreatments(): Collection
    {
        return $this->projectTrailTreatments;
    }

    public function addProjectTrailTreatment(ProjectTrailTreatment $projectTrailTreatment): self
    {
        if (!$this->projectTrailTreatments->contains($projectTrailTreatment)) {
            $this->projectTrailTreatments[] = $projectTrailTreatment;
            $projectTrailTreatment->setProject($this);
        }

        return $this;
    }

    public function removeProjectTrailTreatment(ProjectTrailTreatment $projectTrailTreatment): self
    {
        if ($this->projectTrailTreatments->removeElement($projectTrailTreatment)) {
            // set the owning side to null (unless already changed)
            if ($projectTrailTreatment->getProject() === $this) {
                $projectTrailTreatment->setProject(null);
            }
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
     * @return $this
     */
    public function addDeviation(Deviation $deviation): self
    {
        if (!$this->deviations->contains($deviation)) {
            $this->deviations[] = $deviation;
            $deviation->setProject($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeDeviation(Deviation $deviation): self
    {
        if ($this->deviations->removeElement($deviation)) {
            // set the owning side to null (unless already changed)
            if ($deviation->getProject() === $this) {
                $deviation->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReportVisit[]
     */
    public function getReportVisits(): Collection
    {
        return $this->reportVisits;
    }

    /**
     * @param ReportVisit $reportVisit
     * @return $this
     */
    public function addReportVisit(ReportVisit $reportVisit): self
    {
        if (!$this->reportVisits->contains($reportVisit)) {
            $this->reportVisits[] = $reportVisit;
            $reportVisit->setProject($this);
        }

        return $this;
    }

    /**
     * @param ReportVisit $reportVisit
     *
     * @return $this
     */
    public function removeReportVisit(ReportVisit $reportVisit): self
    {
        if ($this->reportVisits->removeElement($reportVisit)) {
            // set the owning side to null (unless already changed)
            if ($reportVisit->getProject() === $this) {
                $reportVisit->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReportConfig[]
     */
    public function getReportConfigs(): Collection
    {
        return $this->reportVisits;
    }

    /**
     * @param ReportConfig $config
     * @return $this
     */
    public function addReportConfig(ReportConfig $config): self
    {
        if (!$this->reportConfigs->contains($config)) {
            $this->reportConfigs[] = $config;
            $config->setProject($this);
        }

        return $this;
    }

    /**
     * @param ReportConfig $config
     *
     * @return $this
     */
    public function removeReportConfig(ReportConfig $config): self
    {
        if ($this->reportConfigs->removeElement($config)) {
            // set the owning side to null (unless already changed)
            if ($config->getProject() === $this) {
                $config->setProject(null);
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

    /**
     * @return ArrayCollection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    /**
     * @param Document $document
     * @return $this
     */
    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setProject($this);
        }

        return $this;
    }

    /**
     * @param Document $document
     * @return $this
     */
    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getProject() === $this) {
                $document->setProject(null);
            }
        }

        return $this;
    }
}
