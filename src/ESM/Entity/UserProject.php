<?php

namespace App\ESM\Entity;

use App\ESM\Repository\UserProjectRepository;
use App\ESM\Service\AuditTrail\AuditrailableInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserProjectRepository::class)
 */
class UserProject implements AuditrailableInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     * @Groups({"userProject"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userProjects")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var User
     * @Groups({"userProject"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="userProjects")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Project
     */
    private $project;

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
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     */
    private $sourceId;

    /**
     * @ORM\Column(type="json")
     *
     * @var array
     */
    private $metas = [];

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int|null
     */
    private $rate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFieldsToBeIgnored(): array
    {
        return ['user', 'project', 'metas'/*'source_id'*/];
    }

    public function __toString()
    {
        return $this->user->getFullName();
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(?int $rate): void
    {
        $this->rate = $rate;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
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

    public function getSourceId(): int
    {
        return $this->sourceId;
    }

    public function setSourceId(int $sourceId): void
    {
        $this->sourceId = $sourceId;
    }

    public function getMetas(): array
    {
        return $this->metas;
    }

    public function setMetas(array $metas): void
    {
        $this->metas = $metas;
    }
}
