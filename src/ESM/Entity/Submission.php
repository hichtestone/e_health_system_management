<?php

namespace App\ESM\Entity;

use App\ESM\Entity\DropdownList\Country;
use App\ESM\Entity\DropdownList\NameSubmissionRegulatory;
use App\ESM\Entity\DropdownList\TypeDeclaration;
use App\ESM\Entity\DropdownList\TypeSubmission;
use App\ESM\Entity\DropdownList\TypeSubmissionRegulatory;
use App\ESM\Repository\SubmissionRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SubmissionRepository::class)
 */
class Submission implements AuditrailableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $amendmentNumber;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime|null
     */
    private $estimatedSubmissionAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $submissionAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $question = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fileNumber;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $admissibilityAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $authorizationDeadlineAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $authorizationAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startedAt;

    /**
     * @ORM\ManyToOne(targetEntity=NameSubmissionRegulatory::class, inversedBy="submissions")
     * @ORM\JoinColumn(name="nameSubmissionRegulatory_id", referencedColumnName="id", nullable=true)
     * @Assert\NotBlank()
     */
    private $nameSubmissionRegulatory;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Project
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity=Country::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Country
     */
    private $country;

    /**
     * @ORM\ManyToOne(targetEntity=TypeSubmissionRegulatory::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @var TypeSubmissionRegulatory
     */
    private $typeSubmissionRegulatory;

    /**
     * @ORM\ManyToOne(targetEntity=TypeSubmission::class)
     * @ORM\JoinColumn(nullable=true)
     *
     * @var TypeSubmission
     */
    private $typeSubmission;

    /**
     * @ORM\ManyToOne(targetEntity=TypeDeclaration::class)
     * @ORM\JoinColumn(nullable=true)
     *
     * @var TypeDeclaration
     */
    private $typeDeclaration;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 *
	 * @var DateTime|null
	 */
	private $deletedAt;

    public function __toString(): ?string
    {
        return $this->country;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFieldsToBeIgnored(): array
    {
        return [];
    }

    public function getNameSubmissionRegulatory(): ?NameSubmissionRegulatory
    {
        return $this->nameSubmissionRegulatory;
    }

    public function setNameSubmissionRegulatory(?NameSubmissionRegulatory $nameSubmissionRegulatory): void
    {
        $this->nameSubmissionRegulatory = $nameSubmissionRegulatory;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    public function getAmendmentNumber()
    {
        return $this->amendmentNumber;
    }

    public function setAmendmentNumber($amendmentNumber): void
    {
        $this->amendmentNumber = $amendmentNumber;
    }

    public function getEstimatedSubmissionAt(): ?DateTime
    {
        return $this->estimatedSubmissionAt;
    }

    public function setEstimatedSubmissionAt(DateTime $estimatedSubmissionAt): self
    {
        $this->estimatedSubmissionAt = $estimatedSubmissionAt;

        return $this;
    }

    public function getSubmissionAt(): ?DateTime
    {
        return $this->submissionAt;
    }

    public function setSubmissionAt(DateTime $submissionAt): self
    {
        $this->submissionAt = $submissionAt;

        return $this;
    }

    public function getQuestion(): ?bool
    {
        return $this->question;
    }

    public function setQuestion(bool $question): self
    {
        $this->question = $question;

        return $this;
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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getFileNumber(): ?string
    {
        return $this->fileNumber;
    }

    public function setFileNumber(string $fileNumber): self
    {
        $this->fileNumber = $fileNumber;

        return $this;
    }

    public function getAdmissibilityAt(): ?DateTime
    {
        return $this->admissibilityAt;
    }

    public function setAdmissibilityAt(?DateTime $admissibilityAt): self
    {
        $this->admissibilityAt = $admissibilityAt;

        return $this;
    }

    public function getAuthorizationDeadlineAt(): ?DateTime
    {
        return $this->authorizationDeadlineAt;
    }

    public function computeAuthorizationDeadlineAt(): ?DateTime
    {
        $regulatory = $this->getNameSubmissionRegulatory();
        if (null !== $regulatory && null !== $this->admissibilityAt) {
            $days = $this->question ? $regulatory->getDeadlineWithQuestion() : $regulatory->getDeadline();
            $d = clone $this->admissibilityAt;
            $d->add(new \DateInterval('P'.$days.'D'));
            $this->authorizationDeadlineAt = $d;
        }

        return null;
    }

    public function getAuthorizationAt(): ?DateTime
    {
        return $this->authorizationAt;
    }

    public function setAuthorizationAt(?DateTime $authorizationAt): self
    {
        $this->authorizationAt = $authorizationAt;

        return $this;
    }

    public function getStartedAt(): ?DateTime
    {
        return $this->startedAt;
    }

    public function setStartedAt(?DateTime $startedAt): self
    {
        $this->startedAt = $startedAt;

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

    public function getTypeSubmissionRegulatory(): ?TypeSubmissionRegulatory
    {
        return $this->typeSubmissionRegulatory;
    }

    public function setTypeSubmissionRegulatory(?TypeSubmissionRegulatory $typeSubmissionRegulatory): self
    {
        $this->typeSubmissionRegulatory = $typeSubmissionRegulatory;

        return $this;
    }

    public function getTypeSubmission(): ?TypeSubmission
    {
        return $this->typeSubmission;
    }

    public function setTypeSubmission(?TypeSubmission $typeSubmission): self
    {
        $this->typeSubmission = $typeSubmission;

        return $this;
    }

    public function getTypeDeclaration(): ?TypeDeclaration
    {
        return $this->typeDeclaration;
    }

    public function setTypeDeclaration(?TypeDeclaration $typeDeclaration): self
    {
        $this->typeDeclaration = $typeDeclaration;

        return $this;
    }
}
