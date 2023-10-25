<?php

namespace App\ESM\Entity\DropdownList;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="dl_submission_type_authority")
 */
class TypeSubmissionRegulatory
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
     * @ORM\ManyToOne(targetEntity=Country::class, inversedBy="typeSubmissionRegulatory")
     * @ORM\JoinColumn(nullable=true)
     */
    private $country;

    /**
     * @ORM\OneToMany(targetEntity=NameSubmissionRegulatory::class, mappedBy="typeSubmissionRegulatory")
     *
     * @var ArrayCollection<int, NameSubmissionRegulatory>
     */
    private $nameSubmissionRegulatory;

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

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): void
    {
        $this->country = $country;
    }

    /**
     * @return ArrayCollection<int, NameSubmissionRegulatory>
     */
    public function getNameSubmissionRegulatories(): Collection
    {
        return $this->nameSubmissionRegulatory;
    }

    public function addNameSubmissionRegulatories(NameSubmissionRegulatory $nameSubmissionRegulatory): self
    {
        if (!$this->nameSubmissionRegulatory->contains($nameSubmissionRegulatory)) {
            $this->nameSubmissionRegulatory[] = $nameSubmissionRegulatory;
        }

        return $this;
    }

    public function removeNameSubmissionRegulatories(NameSubmissionRegulatory $nameSubmissionRegulatory): self
    {
        if ($this->nameSubmissionRegulatory->contains($nameSubmissionRegulatory)) {
            $this->nameSubmissionRegulatory->removeElement($nameSubmissionRegulatory);
        }

        return $this;
    }
}
