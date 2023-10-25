<?php

namespace App\ESM\Service\AuditTrail;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractAuditTrailEntity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     *
     * @var UserInterface|null
     */
    protected $user;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    protected $date;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     *
     * @var string|null
     */
    protected $reason;

    /**
     * @ORM\Column(type="smallint", options={"comment": "1=insert, 2=update, 3=delete"})
     *
     * @var int
     */
    protected $modif_type;

    /**
     * @ORM\Column(type="json", nullable=true)
     *
     * @var array
     */
    protected $details;

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): void
    {
        $this->reason = $reason;
    }

    public function getModifType(): int
    {
        return $this->modif_type;
    }

    public function setModifType(int $modif_type): void
    {
        $this->modif_type = $modif_type;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function setDetails(array $details): void
    {
        $this->details = $details;
    }
}
