<?php

namespace App\ETMF\Entity\DropdownList;

use App\ETMF\Entity\Artefact;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="artefact_level")
 */
class ArtefactLevel
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     * @Groups({"artefactLevel"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     * @Groups({"artefactLevel"})
     */
    private $level;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     * @Groups({"artefactLevel"})
     */
    private $code;

    /**
     * @ORM\ManyToMany(targetEntity=Artefact::class, mappedBy="artefactLevels")
     * @ORM\JoinTable(name="artefact_artefactLevel")
     *
     * @var ArrayCollection<int, Artefact>
     */
    private $artefacts;

    public function __construct()
    {
        $this->artefacts = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->level;
    }

    /**
     * @return string|null
     */
    public function getLevel(): ?string
    {
        return $this->level;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setLevel(string $name): self
    {
        $this->level = $name;

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
     * @return ArrayCollection<int, Artefact>
     */
    public function getArtefacts(): Collection
    {
        return $this->artefacts;
    }

    /**
     * @param Artefact $artefact
     * @return $this
     */
    public function addArtefact(Artefact $artefact): self
    {
        if (!$this->artefacts->contains($artefact)) {
            $this->artefacts[] = $artefact;
        }

        return $this;
    }

    /**
     * @param Artefact $artefact
     * @return $this
     */
    public function removeArtefact(Artefact $artefact): self
    {
        if ($this->artefacts->contains($artefact)) {
            $this->artefacts->removeElement($artefact);
        }

        return $this;
    }

}
