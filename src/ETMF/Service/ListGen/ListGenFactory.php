<?php

namespace App\ETMF\Service\ListGen;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class ListGenFactory
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var Security
     */
    private $security;

    /**
     * ListGenFactory constructor.
     */
    public function __construct(TranslatorInterface $translator, EntityManagerInterface $entityManager, UrlGeneratorInterface $router, Security $security)
    {
        $this->router = $router;
        $this->translator = $translator;
        $this->em = $entityManager;
        $this->security = $security;
    }

    /**
     * créé une nouvelle instance de ListGenType.
     */
    public function getListGen(string $type): AbstractListGenType
    {
        $c = new $type($this->translator, $this->em, $this->router, $this->security);
        $c->setDependancies();

        return $c;
    }
}
