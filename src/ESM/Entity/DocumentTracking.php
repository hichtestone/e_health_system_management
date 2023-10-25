<?php

namespace App\ESM\Entity;

use App\ESM\Entity\DropdownList\Country;
use App\ESM\Repository\DocumentTrackingRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=DocumentTrackingRepository::class)
 * @UniqueEntity(
 *     fields={"project", "country", "title", "version"},
 *     message="Ce suivi de document existe déjà."
 *  )
 */
class DocumentTracking implements AuditrailableInterface
{
    public const levelCenter = 1;
    public const levelInterlocutor = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class)
     *
     * @var Project
     */
    private $project;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=8)
     *
     * @var string
     */
    private $version;

    /**
     * @ORM\ManyToOne(targetEntity=Country::class)
     *
     * @var Country
     */
    private $country;

    /**
     * @ORM\Column(type="smallint", options={"comment"="1: center, 2: interlocutor"}, nullable=false)
     *
     * @var int
     */
    private $level;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $toBeSent = false;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $toBeReceived = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime
     */
    private $disabledAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }

    public function getFieldsToBeIgnored(): array
    {
        return ['project'];
    }

    public function isInv(): bool
    {
        return self::levelInterlocutor === $this->level;
    }

    public function isCenter(): bool
    {
        return self::levelCenter === $this->level;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): void
    {
        $this->country = $country;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    public function isToBeSent(): bool
    {
        return $this->toBeSent;
    }

    public function setToBeSent(bool $toBeSent): void
    {
        $this->toBeSent = $toBeSent;
    }

    public function isToBeReceived(): bool
    {
        return $this->toBeReceived;
    }

    public function setToBeReceived(bool $toBeReceived): void
    {
        $this->toBeReceived = $toBeReceived;
    }

    public function getDisabledAt(): ?DateTime
    {
        return $this->disabledAt;
    }

    public function setDisabledAt(bool $disabledAt): void
    {
        $this->disabledAt = $disabledAt;
    }
}
