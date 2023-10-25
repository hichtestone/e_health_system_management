<?php

namespace App\ESM\Entity\DropdownList;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="dl_country_department")
 */
class CountryDepartment
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
     * @ORM\Column(type="string", length=3, nullable=true)
     *
     * @var string
     */
    private $code;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $deletedAt;

    /**
     * @ORM\ManyToOne(targetEntity=CountryDepartment::class, inversedBy="children")
     *
     * @var CountryDepartment
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity=CountryDepartment::class, mappedBy="parent")
     * @ORM\OrderBy({"position" = "ASC"})
     *
     * @var ArrayCollection<int, CountryDepartment>
     */
    private $children;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity=Country::class)
     * @ORM\JoinColumn(nullable=true)
     *
     * @var Country
     */
    private $country;

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getCanceropole(): ?self
    {
        return $this->getParent();
    }

    /**
     * @return ArrayCollection<int, CountryDepartment>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addRole(self $countryDepartment): self
    {
        if (!$this->children->contains($countryDepartment)) {
            $this->children[] = $countryDepartment;
            $countryDepartment->setParent($this);
        }

        return $this;
    }

    public function removeRole(self $countryDepartment): self
    {
        if ($this->children->contains($countryDepartment)) {
            $this->children->removeElement($countryDepartment);
            // set the owning side to null (unless already changed)
            if ($countryDepartment->getParent() === $this) {
                $countryDepartment->setParent(null);
            }
        }

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): self
    {
        $this->country = $country;

        return $this;
    }
}
