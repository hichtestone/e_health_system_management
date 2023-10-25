<?php

namespace App\ESM\Entity;

use App\ESM\Repository\PorteeDocumentTransverseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PorteeDocumentTransverseRepository::class)
 */
class PorteeDocumentTransverse
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
     * @ORM\OneToMany(targetEntity=DocumentTransverse::class, mappedBy="porteeDocument")
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
            $documentTransverse->setPorteeDocument($this);
        }

        return $this;
    }

    public function removeDocumentTransverse(DocumentTransverse $documentTransverse): self
    {
        if ($this->documentTransverses->removeElement($documentTransverse)) {
            // set the owning side to null (unless already changed)
            if ($documentTransverse->getPorteeDocument() === $this) {
                $documentTransverse->setPorteeDocument(null);
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
