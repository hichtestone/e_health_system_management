<?php

declare(strict_types=1);

namespace App\ESM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ApplicationType.
 *
 * @ORM\Entity(repositoryClass="App\ESM\Repository\ApplicationTypeRepository")
 */
class ApplicationType
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
    private $name;

	/**
	 * @return int
	 */
    public function getId(): int
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
}
