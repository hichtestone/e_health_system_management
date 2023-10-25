<?php

namespace App\ESM\Twig;

use App\ESM\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('preg_match', [$this, 'pregMatch']),
            new TwigFunction('get_project', [$this, 'getProject']),
            new TwigFunction('get_project_id', [$this, 'getProjectId']),
        ];
    }

    public function pregMatch(string $pattern, string $subject, int $index = 0): string
    {
        preg_match($pattern, $subject, $matches);
        if (count($matches) >= $index + 2) {
            return $matches[$index + 1];
        }

        return '';
    }

    /**
     * Projet ID en cours a partir de l'URL courante.
     */
    public function getProjectId(string $url): int
    {
        $str = preg_match('#\/projects\/([0-9]*)\/#', $url, $m);
        $id = (int) $m[1];

        return $id;
    }

    /**
     * Projet en cours a partir de l'URL courante.
     */
    public function getProject(string $url): Project
    {
        // Id Projet
        $id = $this->getProjectId($url);

        return $this->em->getRepository(Project::class)->find($id);
    }
}
