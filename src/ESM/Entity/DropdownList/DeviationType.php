<?php

namespace App\ESM\Entity\DropdownList;

use App\ESM\Entity\Deviation;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="dl_type_deviation")
 */
class DeviationType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
	 * @Groups("type")
	 * @var int
     */
    private $id;

    /**
	 * @Groups("type")
     * @ORM\Column(type="string", length=255)
	 *
	 * @var string
     */
    private $type;

    /**
	 * @Groups("type")
     * @ORM\Column(type="string", length=10)
	 *
	 * @var string
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity=DeviationType::class, inversedBy="children", fetch="EAGER")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * @ORM\OrderBy({"position" = "ASC"})
     *
     * @var DeviationType
     */
    private $parent;

    /**
	 * @Groups("type")
     * @ORM\OneToMany(targetEntity=DeviationType::class, mappedBy="parent", fetch="EAGER")
     *
     * @var ArrayCollection<int, DeviationType>
     */
    private $children;

    /**
     * @ORM\OneToMany(targetEntity=Deviation::class, mappedBy="type")
     *
     * @var ArrayCollection<int, Deviation>
     */
    private $deviations;

	/**
	 * @ORM\OneToMany(targetEntity=Deviation::class, mappedBy="subType")
	 *
	 * @var ArrayCollection<int, Deviation>
	 */
	private $deviationsSubType;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $position;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deleteAt;

    /**
     * DeviationType constructor.
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->type;
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
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return $this|null
     */
    public function getParent(): ?self
    {
        return $this->parent;
    }

    /**
     * @param DeviationType|null $parent
     * @return $this
     */
    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return ArrayCollection<int, DeviationType>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @param DeviationType $child
     * @return $this
     */
    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    /**
     * @param DeviationType $child
     * @return $this
     */
    public function removeChild(self $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return $this
     */
    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDeleteAt(): ?DateTime
    {
        return $this->deleteAt;
    }

    /**
     * @param DateTime|null $deleteAt
     * @return $this
     */
    public function setDeleteAt(?DateTime $deleteAt): self
    {
        $this->deleteAt = $deleteAt;

        return $this;
    }

    /**
     * @return ArrayCollection<int, Deviation>
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
            $deviation->setType($this);
        }

        return $this;
    }

    /**
     * @param Deviation $deviation
     * @return $this
     */
    public function removeDeviation(Deviation $deviation): self
    {
        if ($this->children->contains($deviation)) {
            $this->children->removeElement($deviation);
            // set the owning side to null (unless already changed)
            if ($deviation->getType() === $this) {
                $deviation->setType(null);
            }
        }

        return $this;
    }

	/**
	 * @return ArrayCollection<int, Deviation>
	 */
	public function getDeviationsSubType(): Collection
	{
		return $this->deviationsSubType;
	}

	/**
	 * @param Deviation $deviation
	 * @return $this
	 */
	public function addDeviationSubType(Deviation $deviation): self
	{
		if (!$this->deviationsSubType->contains($deviation)) {
			$this->deviationsSubType[] = $deviation;
			$deviation->setSubType($this);
		}

		return $this;
	}

	/**
	 * @param Deviation $deviation
	 * @return $this
	 */
	public function removeDeviationSubType(Deviation $deviation): self
	{
		if ($this->deviationsSubType->contains($deviation)) {
			$this->deviationsSubType->removeElement($deviation);
			// set the owning side to null (unless already changed)
			if ($deviation->getSubType() === $this) {
				$deviation->setSubType(null);
			}
		}

		return $this;
	}
}
