<?php

declare(strict_types=1);

namespace App\ESM\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ConnectionAuditTrail.
 *
 * @ORM\Entity(repositoryClass="App\ESM\Repository\ConnectionAuditTrailRepository")
 */
class ConnectionAuditTrail
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    private $started_at;

    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $ended_at;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $user_agent;

    /**
     * @var string
     * @ORM\Column(type="string", length=55, unique=true)
     */
    private $device_session_token;

    /**
     * @var string
     * @ORM\Column(type="string", length=55)
     */
    private $ip;

    /**
     * @var Application
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\Application")
     * @ORM\JoinColumn(nullable=false)
     */
    private $application;

    /**
     * 1=logout, 2=timeout, 3=force.
     *
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $reason;

    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_ping;

    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_refresh;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getStartedAt(): DateTime
    {
        return $this->started_at;
    }

    public function setStartedAt(DateTime $started_at): void
    {
        $this->started_at = $started_at;
    }

    public function getEndedAt(): ?DateTime
    {
        return $this->ended_at;
    }

    public function setEndedAt(DateTime $ended_at): void
    {
        $this->ended_at = $ended_at;
    }

    public function getUseragent(): string
    {
        return $this->user_agent;
    }

    public function setUseragent(string $user_agent): void
    {
        $this->user_agent = $user_agent;
    }

    public function getDeviceSessionToken(): string
    {
        return $this->device_session_token;
    }

    public function setDeviceSessionToken(string $device_session_token): void
    {
        $this->device_session_token = $device_session_token;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function setApplication(Application $application): void
    {
        $this->application = $application;
    }

    public function getReason(): int
    {
        return $this->reason;
    }

    public function setReason(int $reason): void
    {
        $this->reason = $reason;
    }

    public function getLastPing(): ?DateTime
    {
        return $this->last_ping;
    }

    public function setLastPing(?DateTime $last_ping): void
    {
        $this->last_ping = $last_ping;
    }

    public function getLastRefresh(): ?DateTime
    {
        return $this->last_refresh;
    }

    public function setLastRefresh(?DateTime $last_refresh): void
    {
        $this->last_refresh = $last_refresh;
    }
}
