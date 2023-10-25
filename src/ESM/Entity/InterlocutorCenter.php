<?php

namespace App\ESM\Entity;

use App\ESM\Repository\InterlocutorCenterRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=InterlocutorCenterRepository::class)
 * @UniqueEntity(
 *     fields={"center", "interlocutor", "service"},
 *     message="Ce Numéro de centre {{ value }} est déjà utilisé."
 *  )
 */
class InterlocutorCenter implements AuditrailableInterface
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
     * @ORM\ManyToOne(targetEntity=Center::class, inversedBy="interlocutorCenters")
     * @ORM\JoinColumn(nullable=true)
     *
     * @var Center
     */
    private $center;

    /**
     * @ORM\ManyToOne(targetEntity=Interlocutor::class, inversedBy="interlocutorCenters")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Interlocutor
     */
    private $interlocutor;

    /**
     * @ORM\ManyToOne(targetEntity=Service::class, inversedBy="interlocutorCenters")
     * @ORM\JoinColumn(nullable=true)
     *
     * @var Service
     */
    private $service;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $enabledAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $disabledAt;

    /**
     * @ORM\Column(type="json")
     *
     * @var array
     */
    private $metas = [];

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     */
    private $sourceId;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $isPrincipalInvestigator = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFieldsToBeIgnored(): array
    {
        return ['center', 'interlocutor'];
    }

    public function __toString()
    {
        return $this->center->getName() ?? '';
    }

    public function getCenter(): Center
    {
        return $this->center;
    }

    public function setCenter(Center $center): void
    {
        $this->center = $center;
    }

    public function getInterlocutor(): ?Interlocutor
    {
        return $this->interlocutor;
    }

    public function setInterlocutor(Interlocutor $interlocutor): void
    {
        $this->interlocutor = $interlocutor;
    }

    public function isPrincipalInvestigator(): bool
    {
        return $this->isPrincipalInvestigator;
    }

    public function setIsPrincipalInvestigator(bool $isPrincipalInvestigator): void
    {
        $this->isPrincipalInvestigator = $isPrincipalInvestigator;
    }

    public function getEnabledAt(): ?DateTime
    {
        return $this->enabledAt;
    }

    public function setEnabledAt(?DateTime $enabledAt): void
    {
        $this->enabledAt = $enabledAt;
    }

    public function getDisabledAt(): ?DateTime
    {
        return $this->disabledAt;
    }

    public function setDisabledAt(?DateTime $disabledAt): void
    {
        $this->disabledAt = $disabledAt;
    }

    public function getMetas(): array
    {
        return $this->metas;
    }

    public function setMetas(array $metas): void
    {
        $this->metas = $metas;
    }

    public function getSourceId(): int
    {
        return $this->sourceId;
    }

    public function setSourceId(int $sourceId): void
    {
        $this->sourceId = $sourceId;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(Service $service): void
    {
        $this->service = $service;
    }
}
