<?php

namespace App\ETMF\Entity\DropdownList;

use App\ESM\Entity\Project;
use App\ETMF\Entity\Artefact;
use App\ETMF\Entity\Document;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="document_level")
 */
class DocumentLevel
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
    private $level;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $code;

    /**
     * @ORM\ManyToMany(targetEntity=Document::class, mappedBy="documentLevels")
     * @ORM\JoinTable(name="document_artefactLevel")
     * @var ArrayCollection<int, Document>
     */
    private $documents;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
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
     * @return ArrayCollection<int, Document>
     */
    public function getArtefacts(): Collection
    {
        return $this->documents;
    }

    /**
     * @param Document $document
     * @return $this
     */
    public function addArtefact(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
        }

        return $this;
    }

    /**
     * @param Document $document
     * @return $this
     */
    public function removeArtefact(Document $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
        }

        return $this;
    }

}
