<?php

namespace App\ESM\Entity;

use App\ESM\Repository\DelegationRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DelegationRepository::class)
 */
class Delegation implements AuditrailableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $repSponsor;

    /**
     * @ORM\Column(type="boolean")
     */
    private $regulatory = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $manitoring = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $pharmacovigilance = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $dsur = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $susar = false;

    public function __toString()
    {
        return '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRepSponsor(): ?string
    {
        return $this->repSponsor;
    }

    public function setRepSponsor(?string $repSponsor): self
    {
        $this->repSponsor = $repSponsor;

        return $this;
    }

    public function getRegulatory(): ?bool
    {
        return $this->regulatory;
    }

    public function setRegulatory(bool $regulatory): self
    {
        $this->regulatory = $regulatory;

        return $this;
    }

    public function getManitoring(): ?bool
    {
        return $this->manitoring;
    }

    public function setManitoring(bool $manitoring): self
    {
        $this->manitoring = $manitoring;

        return $this;
    }

    public function getPharmacovigilance(): ?bool
    {
        return $this->pharmacovigilance;
    }

    public function setPharmacovigilance(bool $pharmacovigilance): self
    {
        $this->pharmacovigilance = $pharmacovigilance;

        return $this;
    }

    public function getFieldsToBeIgnored(): array
    {
        return [];
    }

    public function isDsur(): bool
    {
        return $this->dsur;
    }

    public function setDsur(bool $dsur): void
    {
        $this->dsur = $dsur;
    }

    public function isSusar(): bool
    {
        return $this->susar;
    }

    public function setSusar(bool $susar): void
    {
        $this->susar = $susar;
    }
}
