<?php

declare(strict_types=1);

namespace App\ESM\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Application.
 *
 * @ORM\Entity(repositoryClass="App\ESM\Repository\ApplicationRepository")
 */
class Application
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=55)
     */
    private $name = '';

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $url = '';

    /**
     * @var string
     * @ORM\Column(type="string", length=55)
     */
    private $img;

    /**
     * @var ApplicationType
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\ApplicationType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $api_token = '';

    /**
     * @var User[]
     * @ORM\ManyToMany(targetEntity="App\ESM\Entity\User")
     */
    private $users;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deleted_at;

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
    public function setId(int $id): void
    {
        $this->id = $id;
    }

	/**
	 * @return string
	 */
    public function getName(): string
    {
        return $this->name;
    }

	/**
	 * @param string $name
	 */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

	/**
	 * @return string
	 */
    public function getUrl(): string
    {
        return $this->url;
    }

	/**
	 * @param string $url
	 */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

	/**
	 * @return string
	 */
    public function getImg(): string
    {
        return $this->img;
    }

	/**
	 * @param string $img
	 */
    public function setImg(string $img): void
    {
        $this->img = $img;
    }

    /**
     * @return ApplicationType
     */
    public function getType(): ?ApplicationType
    {
        return $this->type;
    }

	/**
	 * @param ApplicationType $type
	 */
    public function setType(ApplicationType $type): void
    {
        $this->type = $type;
    }

	/**
	 * @return string
	 */
    public function getApiToken(): string
    {
        return $this->api_token;
    }

	/**
	 * @param string $api_token
	 */
    public function setApiToken(string $api_token): void
    {
        $this->api_token = $api_token;
    }

    /**
     * @return User[]
     */
    public function getUsers(): iterable
    {
        return $this->users;
    }

	/**
	 * @return DateTime|null
	 */
    public function getDeletedAt(): ?DateTime
    {
        return $this->deleted_at;
    }

	/**
	 * @param DateTime|null $deleted_at
	 */
    public function setDeletedAt(?DateTime $deleted_at): void
    {
        $this->deleted_at = $deleted_at;
    }

	/**
	 * @param User $user
	 * @return bool
	 */
    public function hasUser(User $user): bool
    {
        foreach ($this->getUsers() as $oUser) {
            if ($user->getId() === $oUser->getId()) {
                return true;
            }
        }

        return false;
    }

	/**
	 * @param User $user
	 */
    public function removeUser(User $user): void
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeApplication($this);
        }
    }

	/**
	 * @param User $user
	 */
    public function addUser(User $user): void
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addApplication($this);
        }
    }
}
