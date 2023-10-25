<?php

namespace App\ESM\Entity;

use App\ESM\Repository\DateRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DateRepository::class)
 */
class Date implements AuditrailableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $submissionAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $feasibilityCommitteeAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $reviewCommitteeAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $registrationAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $subscriptionStartedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $subscriptionEndedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $certificationAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $kiffOfMeetingAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $mepForecastAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $mepAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $forecastInclusionStartedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $firstConsentAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $inclusionPatientStartedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $forecastInclusionEndedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $inclusionPatientEndedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $studyDeclarationEndedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $validationCommmitteeReviewEndedAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $amendmentsBeforFirstInclusion;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberExpectedPatients;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberScreenedPatients;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberPatientsIncluded;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberRandomizedPatients;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $expectedDurationInclusionAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $expectedDurationFollowUpAfterInclusionAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expectedLPLVAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $actualLPLVAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expectedReportAnalysedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $actualReportAnalysedtAt;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $finalReportAnalysedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $finalExpectedLPLVAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $finalActualLPLVAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $finalExpectedReportAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $finalActualReportAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $finalActualReportClinicalAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $depotClinicalTrialsAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $depotEudraCtAt;

    /**
     * @ORM\OneToMany(targetEntity=ProjectDatabaseFreeze::class, mappedBy="projectDate")
     *
     * @var ArrayCollection<int, ProjectDatabaseFreeze>
     */
    private $databaseFreezes;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Project
     */
    private $project;

    public function __construct()
    {
        $this->databaseFreezes = new ArrayCollection();
    }

    public function getDatabaseFreezes(): Collection
    {
        return $this->databaseFreezes;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): ?string
    {
        return $this->getProject()->getName();
    }

    public function getFieldsToBeIgnored(): array
    {
        return ['amendmentsBeforFirstInclusion'];
    }

    public function getSubmissionAt(): ?DateTime
    {
        return $this->submissionAt;
    }

    public function setSubmissionAt(?DateTime $submissionAt): self
    {
        $this->submissionAt = $submissionAt;

        return $this;
    }

    public function getFeasibilityCommitteeAt(): ?DateTime
    {
        return $this->feasibilityCommitteeAt;
    }

    public function setFeasibilityCommitteeAt(?DateTime $feasibilityCommitteeAt): self
    {
        $this->feasibilityCommitteeAt = $feasibilityCommitteeAt;

        return $this;
    }

    public function getReviewCommitteeAt(): ?DateTime
    {
        return $this->reviewCommitteeAt;
    }

    public function setReviewCommitteeAt(?DateTime $reviewCommitteeAt): self
    {
        $this->reviewCommitteeAt = $reviewCommitteeAt;

        return $this;
    }

    public function getRegistrationAt(): ?DateTime
    {
        return $this->registrationAt;
    }

    public function setRegistrationAt(?DateTime $registrationAt): self
    {
        $this->registrationAt = $registrationAt;

        return $this;
    }

    public function getSubscriptionStartedAt(): ?DateTime
    {
        return $this->subscriptionStartedAt;
    }

    public function setSubscriptionStartedAt(?DateTime $subscriptionStartedAt): self
    {
        $this->subscriptionStartedAt = $subscriptionStartedAt;

        return $this;
    }

    public function getSubscriptionEndedAt(): ?DateTime
    {
        return $this->subscriptionEndedAt;
    }

    public function setSubscriptionEndedAt(?DateTime $subscriptionEndedAt): self
    {
        $this->subscriptionEndedAt = $subscriptionEndedAt;

        return $this;
    }

    public function getCertificationAt(): ?DateTime
    {
        return $this->certificationAt;
    }

    public function setCertificationAt(?DateTime $certificationAt): self
    {
        $this->certificationAt = $certificationAt;

        return $this;
    }

    public function getKiffOfMeetingAt(): ?DateTime
    {
        return $this->kiffOfMeetingAt;
    }

    public function setKiffOfMeetingAt(?DateTime $kiffOfMeetingAt): self
    {
        $this->kiffOfMeetingAt = $kiffOfMeetingAt;

        return $this;
    }

    public function getMepForecastAt(): ?DateTime
    {
        return $this->mepForecastAt;
    }

    public function setMepForecastAt(?DateTime $mepForecastAt): self
    {
        $this->mepForecastAt = $mepForecastAt;

        return $this;
    }

    public function getMepAt(): ?DateTime
    {
        return $this->mepAt;
    }

    public function setMepAt(?DateTime $mepAt): self
    {
        $this->mepAt = $mepAt;

        return $this;
    }

    public function getForecastInclusionStartedAt(): ?DateTime
    {
        return $this->forecastInclusionStartedAt;
    }

    public function setForecastInclusionStartedAt(?DateTime $forecastInclusionStartedAt): self
    {
        $this->forecastInclusionStartedAt = $forecastInclusionStartedAt;

        return $this;
    }

    public function getForecastInclusionEndedAt(): ?DateTime
    {
        return $this->forecastInclusionEndedAt;
    }

    public function setForecastInclusionEndedAt(?DateTime $forecastInclusionEndedAt): self
    {
        $this->forecastInclusionEndedAt = $forecastInclusionEndedAt;

        return $this;
    }

    public function getFirstConsentAt(): ?DateTime
    {
        return $this->firstConsentAt;
    }

    public function setFirstConsentAt(?DateTime $firstConsentAt): self
    {
        $this->firstConsentAt = $firstConsentAt;

        return $this;
    }

    public function getInclusionPatientStartedAt(): ?DateTime
    {
        return $this->inclusionPatientStartedAt;
    }

    public function setInclusionPatientStartedAt(?DateTime $inclusionPatientStartedAt): self
    {
        $this->inclusionPatientStartedAt = $inclusionPatientStartedAt;

        return $this;
    }

    public function getInclusionPatientEndedAt(): ?DateTime
    {
        return $this->inclusionPatientEndedAt;
    }

    public function setInclusionPatientEndedAt(?DateTime $inclusionPatientEndedAt): self
    {
        $this->inclusionPatientEndedAt = $inclusionPatientEndedAt;

        return $this;
    }

    public function getStudyDeclarationEndedAt(): ?DateTime
    {
        return $this->studyDeclarationEndedAt;
    }

    public function setStudyDeclarationEndedAt(?DateTime $studyDeclarationEndedAt): self
    {
        $this->studyDeclarationEndedAt = $studyDeclarationEndedAt;

        return $this;
    }

    public function getValidationCommmitteeReviewEndedAt(): ?DateTime
    {
        return $this->validationCommmitteeReviewEndedAt;
    }

    public function setValidationCommmitteeReviewEndedAt(?DateTime $validationCommmitteeReviewEndedAt): self
    {
        $this->validationCommmitteeReviewEndedAt = $validationCommmitteeReviewEndedAt;

        return $this;
    }

    public function getAmendmentsBeforFirstInclusion(): ?bool
    {
        return $this->amendmentsBeforFirstInclusion;
    }

    public function setAmendmentsBeforFirstInclusion(?bool $amendmentsBeforFirstInclusion): self
    {
        $this->amendmentsBeforFirstInclusion = $amendmentsBeforFirstInclusion;

        return $this;
    }

    public function getNumberExpectedPatients(): ?int
    {
        return $this->numberExpectedPatients;
    }

    public function setNumberExpectedPatients(int $numberExpectedPatients): self
    {
        $this->numberExpectedPatients = $numberExpectedPatients;

        return $this;
    }

    public function getNumberScreenedPatients(): ?int
    {
        return $this->numberScreenedPatients;
    }

    public function setNumberScreenedPatients(?int $numberScreenedPatients): self
    {
        $this->numberScreenedPatients = $numberScreenedPatients;

        return $this;
    }

    public function getNumberPatientsIncluded(): ?int
    {
        return $this->numberPatientsIncluded;
    }

    public function setNumberPatientsIncluded(int $numberPatientsIncluded): self
    {
        $this->numberPatientsIncluded = $numberPatientsIncluded;

        return $this;
    }

    public function getNumberRandomizedPatients(): ?int
    {
        return $this->numberRandomizedPatients;
    }

    public function setNumberRandomizedPatients(?int $numberRandomizedPatients): self
    {
        $this->numberRandomizedPatients = $numberRandomizedPatients;

        return $this;
    }

    public function getExpectedDurationInclusionAt(): ?int
    {
        return $this->expectedDurationInclusionAt;
    }

    public function setExpectedDurationInclusionAt(int $expectedDurationInclusionAt): self
    {
        $this->expectedDurationInclusionAt = $expectedDurationInclusionAt;

        return $this;
    }

    public function getExpectedDurationFollowUpAfterInclusionAt(): ?int
    {
        return $this->expectedDurationFollowUpAfterInclusionAt;
    }

    public function setExpectedDurationFollowUpAfterInclusionAt(?int $expectedDurationFollowUpAfterInclusionAt): self
    {
        $this->expectedDurationFollowUpAfterInclusionAt = $expectedDurationFollowUpAfterInclusionAt;

        return $this;
    }

    public function getExpectedLPLVAt(): ?DateTime
    {
        return $this->expectedLPLVAt;
    }

    public function setExpectedLPLVAt(?DateTime $expectedLPLVAt): self
    {
        $this->expectedLPLVAt = $expectedLPLVAt;

        return $this;
    }

    public function getActualLPLVAt(): ?DateTime
    {
        return $this->actualLPLVAt;
    }

    public function setActualLPLVAt(?DateTime $actualLPLVAt): self
    {
        $this->actualLPLVAt = $actualLPLVAt;

        return $this;
    }

    public function getExpectedReportAnalysedAt(): ?DateTime
    {
        return $this->actualLPLVAt;
    }

    public function setExpectedReportAnalysedAt(?DateTime $expectedReportAnalysedAt): self
    {
        $this->expectedReportAnalysedAt = $expectedReportAnalysedAt;

        return $this;
    }

    public function getActualReportAnalysedtAt(): ?DateTime
    {
        return $this->actualReportAnalysedtAt;
    }

    public function setActualReportAnalysedtAt(?DateTime $actualReportAnalysedtAt): self
    {
        $this->actualReportAnalysedtAt = $actualReportAnalysedtAt;

        return $this;
    }

    public function getFinalReportAnalysedAt(): ?DateTime
    {
        return $this->finalReportAnalysedAt;
    }

    public function setFinalReportAnalysedAt(?DateTime $finalReportAnalysedAt): self
    {
        $this->finalReportAnalysedAt = $finalReportAnalysedAt;

        return $this;
    }

    public function getFinalExpectedLPLVAt(): ?DateTime
    {
        return $this->finalExpectedLPLVAt;
    }

    public function setFinalExpectedLPLVAt(?DateTime $finalExpectedLPLVAt): self
    {
        $this->finalExpectedLPLVAt = $finalExpectedLPLVAt;

        return $this;
    }

    public function getFinalActualLPLVAt(): ?DateTime
    {
        return $this->finalActualLPLVAt;
    }

    public function setFinalActualLPLVAt(?DateTime $finalActualLPLVAt): self
    {
        $this->finalActualLPLVAt = $finalActualLPLVAt;

        return $this;
    }

    public function getFinalExpectedReportAt(): ?DateTime
    {
        return $this->finalExpectedReportAt;
    }

    public function setFinalExpectedReportAt(?DateTime $finalExpectedReportAt): self
    {
        $this->finalExpectedReportAt = $finalExpectedReportAt;

        return $this;
    }

    public function getFinalActualReportAt(): ?DateTime
    {
        return $this->finalActualReportAt;
    }

    public function setFinalActualReportAt(?DateTime $finalActualReportAt): self
    {
        $this->finalActualReportAt = $finalActualReportAt;

        return $this;
    }

    public function getFinalActualReportClinicalAt(): ?DateTime
    {
        return $this->finalActualReportClinicalAt;
    }

    public function setFinalActualReportClinicalAt(?DateTime $finalActualReportClinicalAt): self
    {
        $this->finalActualReportClinicalAt = $finalActualReportClinicalAt;

        return $this;
    }

    public function getDepotClinicalTrialsAt(): ?DateTime
    {
        return $this->depotClinicalTrialsAt;
    }

    public function setDepotClinicalTrialsAt(?DateTime $depotClinicalTrialsAt): self
    {
        $this->depotClinicalTrialsAt = $depotClinicalTrialsAt;

        return $this;
    }

    public function getDepotEudraCtAt(): ?DateTime
    {
        return $this->depotEudraCtAt;
    }

    public function setDepotEudraCtAt(?DateTime $depotEudraCtAt): self
    {
        $this->depotEudraCtAt = $depotEudraCtAt;

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
}
