<?php

declare(strict_types=1);

namespace App\ESM\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ConnexionErrorAuditTrail.
 *
 * @ORM\Entity(repositoryClass="App\ESM\Repository\ConnectionErrorAuditTrailRepository")
 */
class ConnectionErrorAuditTrail
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $device;

    /**
     * @var string
     * @ORM\Column(type="string", length=55)
     */
    private $ip;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $error;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $username;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getDevice(): string
    {
        return $this->device;
    }

    public function setDevice(string $device): void
    {
        $this->device = $device;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function setError(string $error): void
    {
        $this->error = $error;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }
}
