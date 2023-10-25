<?php

namespace App\ESM\Entity;

use App\ESM\Repository\DocumentTrackingInterlocutorRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=DocumentTrackingInterlocutorRepository::class)
 * @UniqueEntity(
 *     fields={"interlocutor", "documentTracking"},
 *     message="Ce document est déjà suivi."
 *  )
 */
class DocumentTrackingInterlocutor implements AuditrailableInterface
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
     * @ORM\ManyToOne(targetEntity=Interlocutor::class)
     *
     * @var Interlocutor
     */
    private $interlocutor;

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
        return ['documentTracking', 'interlocutor'];
    }

    public function getDocumentTracking(): ?DocumentTracking
    {
        return $this->documentTracking;
    }

    public function setDocumentTracking(DocumentTracking $documentTracking): void
    {
        $this->documentTracking = $documentTracking;
    }

    public function getInterlocutor(): Interlocutor
    {
        return $this->interlocutor;
    }

    public function setInterlocutor(Interlocutor $interlocutor): void
    {
        $this->interlocutor = $interlocutor;
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
