<?php

namespace App\ESM\Twig;

use App\ESM\Entity\Project;
use App\ESM\Entity\VersionDocumentTransverse;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class DocumentTransverseExtension extends AbstractExtension
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getFilters(): array
    {
        return [

        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('count_versions_by_document_withfile', [$this, 'countVersionsByDocumentWithfile']),
        ];
    }

    public function countVersionsByDocumentWithfile($document): int
    {
        return $this->em->getRepository(VersionDocumentTransverse::class)->countByDocumentWithFile($document);
    }
}
