<?php

namespace App\ESM\Entity;

use App\ESM\Repository\TypeDocumentTransverseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypeDocumentTransverseRepository::class)
 */
class TypeDocumentTransverse
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
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $daysCountValid;

    /**
     * @ORM\OneToMany(targetEntity=DocumentTransverse::class, mappedBy="TypeDocument")
     */
    private $documentTransverses;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    public function __construct()
    {
        $this->documentTransverses = new ArrayCollection();
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

    public function getDaysCountValid(): ?int
    {
        return $this->daysCountValid;
    }

    public function setDaysCountValid(int $daysCountValid): self
    {
        $this->daysCountValid = $daysCountValid;

        return $this;
    }

    /**
     * @return Collection|DocumentTransverse[]
     */
    public function getDocumentTransverses(): Collection
    {
        return $this->documentTransverses;
    }

    public function addDocumentTransverse(DocumentTransverse $documentTransverse): self
    {
        if (!$this->documentTransverses->contains($documentTransverse)) {
            $this->documentTransverses[] = $documentTransverse;
            $documentTransverse->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocumentTransverse(DocumentTransverse $documentTransverse): self
    {
        if ($this->documentTransverses->removeElement($documentTransverse)) {
            // set the owning side to null (unless already changed)
            if ($documentTransverse->getTypeDocument() === $this) {
                $documentTransverse->setTypeDocument(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
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

}
