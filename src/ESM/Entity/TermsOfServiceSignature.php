<?php

declare(strict_types=1);

namespace App\ESM\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class User.
 *
 * @ORM\Entity(repositoryClass="App\ESM\Repository\TermsOfServiceSignatureRepository")
 */
class TermsOfServiceSignature
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
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\User", inversedBy="terms_of_service_signatures")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var TermsOfService
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\TermsOfService", inversedBy="terms_of_service_signatures")
     * @ORM\JoinColumn(nullable=false)
     */
    private $terms_of_service;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    private $signed_at;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return TermsOfService
     */
    public function getTermsOfService()
    {
        return $this->terms_of_service;
    }

    /**
     * @param TermsOfService $terms_of_service
     */
    public function setTermsOfService($terms_of_service): void
    {
        $this->terms_of_service = $terms_of_service;
    }

    /**
     * @return DateTime
     */
    public function getSignedAt()
    {
        return $this->signed_at;
    }

    /**
     * @param DateTime $signed_at
     */
    public function setSignedAt($signed_at): void
    {
        $this->signed_at = $signed_at;
    }
}
