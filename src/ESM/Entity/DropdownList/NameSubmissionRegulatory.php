<?php

namespace App\ESM\Entity\DropdownList;

use App\ESM\Entity\Submission;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="dl_submission_name")
 */
class NameSubmissionRegulatory
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
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $deletedAt;

    /**
     * @ORM\ManyToOne(targetEntity=TypeSubmissionRegulatory::class, inversedBy="nameSubmissionRegulatory")
     * @ORM\JoinColumn(nullable=true)
     */
    private $typeSubmissionRegulatory;

    /**
     * @ORM\OneToMany(targetEntity=Submission::class, mappedBy="nameSubmissionRegulatory")
     *
     * @var ArrayCollection<int, Submission>
     */
    private $submissions;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $deadline;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $deadlineWithQuestion;

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getTypeSubmissionRegulatory(): ?TypeSubmissionRegulatory
    {
        return $this->typeSubmissionRegulatory;
    }

    public function setTypeSubmissionRegulatory(TypeSubmissionRegulatory $typeSubmissionRegulatory): void
    {
        $this->typeSubmissionRegulatory = $typeSubmissionRegulatory;
    }

    /**
     * @return ArrayCollection<int, Submission>
     */
    public function getNameSubmissionRegulatory(): Collection
    {
        return $this->submissions;
    }

    public function addSubmission(Submission $submission): self
    {
        if (!$this->submissions->contains($submission)) {
            $this->submissions[] = $submission;
        }

        return $this;
    }

    public function removeSubmission(Submission $submission): self
    {
        if ($this->submissions->contains($submission)) {
            $this->submissions->removeElement($submission);
        }

        return $this;
    }

    public function getDeadline()
    {
        return $this->deadline;
    }

    public function setDeadline($deadline): void
    {
        $this->deadline = $deadline;
    }

    public function getDeadlineWithQuestion()
    {
        return $this->deadlineWithQuestion;
    }

    public function setDeadlineWithQuestion($deadlineWithQuestion): void
    {
        $this->deadlineWithQuestion = $deadlineWithQuestion;
    }
}
