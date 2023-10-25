<?php

declare(strict_types=1);

namespace App\ESM\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class User.
 *
 * @ORM\Entity(repositoryClass="App\ESM\Repository\TermsOfServiceRepository")
 */
class TermsOfService
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var DateTime
     * @ORM\Column(type="date")
     */
    private $published_at;

    /**
     * @var TermsOfServiceSignature
     * @ORM\OneToMany(targetEntity="App\ESM\Entity\TermsOfServiceSignature", mappedBy="terms_of_service")
     */
    private $terms_of_service_signatures;

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
     * @return DateTime
     */
    public function getPublishedAt()
    {
        return $this->published_at;
    }

    /**
     * @param DateTime $published_at
     */
    public function setPublishedAt($published_at): void
    {
        $this->published_at = $published_at;
    }

    /**
     * @return TermsOfServiceSignature
     */
    public function getTermsOfServiceSignatures()
    {
        return $this->terms_of_service_signatures;
    }

    /**
     * @param TermsOfServiceSignature $terms_of_service_signatures
     */
    public function setTermsOfServiceSignatures($terms_of_service_signatures): void
    {
        $this->terms_of_service_signatures = $terms_of_service_signatures;
    }
}
