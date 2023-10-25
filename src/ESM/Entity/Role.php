<?php

namespace App\ESM\Entity;

use App\ESM\Repository\RoleRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoleRepository::class)
 */
class Role
{
	public const ROLE_DEVIATION_WRITE               = 'ROLE_DEVIATION_WRITE';
	public const ROLE_NO_CONFORMITY_SYSTEM_QA_WRITE = 'ROLE_NO_CONFORMITY_SYSTEM_QA_WRITE';
	public const ROLE_NO_CONFORMITY_SYSTEM_WRITE    = 'ROLE_NO_CONFORMITY_SYSTEM_WRITE';

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
    private $code;

    /**
     * @ORM\ManyToMany(targetEntity=Profile::class, mappedBy="roles")
     *
     * @var ArrayCollection<int, Profile>
     */
    private $profiles;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $deletedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Role::class, inversedBy="children")
     * @ORM\OrderBy({"position" = "ASC"})
     *
     * @var Role
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity=Role::class, mappedBy="parent")
     *
     * @var ArrayCollection<int, Role>
     */
    private $children;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $position;

    public function __construct()
    {
        $this->profiles = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->getCode();
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

    /**
     * @return ArrayCollection<int, Profile>
     */
    public function getProfiles(): Collection
    {
        return $this->profiles;
    }

    public function addProfile(Profile $profile): self
    {
        if (!$this->profiles->contains($profile)) {
            $this->profiles[] = $profile;
            $profile->addRole($this);
        }

        return $this;
    }

    public function removeProfile(Profile $profile): self
    {
        if ($this->profiles->contains($profile)) {
            $this->profiles->removeElement($profile);
            $profile->removeRole($this);
        }

        return $this;
    }

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): self
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

    /**
     * @return ArrayCollection<int, Role>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }
}
