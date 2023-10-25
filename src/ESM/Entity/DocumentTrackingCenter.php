<?php

namespace App\ESM\Entity;

use App\ESM\Repository\DocumentTrackingCenterRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=DocumentTrackingCenterRepository::class)
 * @UniqueEntity(
 *     fields={"center", "documentTracking"},
 *     message="Ce document est déjà suivi."
 *  )
 */
class DocumentTrackingCenter implements AuditrailableInterface
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
     * @ORM\ManyToOne(targetEntity=DocumentTracking::class)
     *
     * @var DocumentTracking
     */
    private $documentTracking;

    /**
     * @ORM\ManyToOne(targetEntity=Center::class)
     *
     * @var Center
     */
    private $center;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime
     */
    private $sentAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime
     */
    private $receivedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->documentTracking->getTitle();
    }

    public function getFieldsToBeIgnored(): array
    {
        return ['documentTracking', 'center'];
    }

    public function getDocumentTracking(): ?DocumentTracking
    {
        return $this->documentTracking;
    }

    public function setDocumentTracking(DocumentTracking $documentTracking): void
    {
        $this->documentTracking = $documentTracking;
    }

    public function getCenter(): Center
    {
        return $this->center;
    }

    public function setCenter(Center $center): void
    {
        $this->center = $center;
    }

    public function getSentAt(): ?DateTime
    {
        return $this->sentAt;
    }

    public function setSentAt(?DateTime $sentAt): void
    {
        $this->sentAt = $sentAt;
    }

    public function getReceivedAt(): ?DateTime
    {
        return $this->receivedAt;
    }

    public function setReceivedAt(?DateTime $receivedAt): void
    {
        $this->receivedAt = $receivedAt;
    }
}
