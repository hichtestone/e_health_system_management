<?php

namespace App\ESM\Entity\DropdownList;

use App\ESM\Entity\Interlocutor;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="dl_cooperator")
 */
class Cooperator
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

	/**
	 * @ORM\ManyToMany(targetEntity=Interlocutor::class, mappedBy="cooperators", cascade={"persist"}, orphanRemoval=false)
	 * @ORM\OrderBy({"lastName" = "ASC"})
	 *
	 * @var ArrayCollection<int, Cooperator>
	 */
	private $interlocutors;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

	public function __construct()
	{
		$this->interlocutors = new ArrayCollection();
	}

	/**
	 * @return int|null
	 */
    public function getId(): ?int
    {
        return $this->id;
    }

	/**
	 * @return mixed
	 */
	public function __toString()
	{
		return $this->title;
	}

	/**
	 * @return string|null
	 */
    public function getTitle(): ?string
    {
        return $this->title;
    }

	/**
	 * @param string $title
	 * @return $this
	 */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
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
	 * @return $this
	 */
    public function setDeletedAt(?DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

	/**
	 * @return ArrayCollection<int, Interlocutor>
	 */
	public function getInterlocutors(): Collection
	{
		return $this->interlocutors;
	}


}
