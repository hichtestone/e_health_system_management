<?php

namespace App\ESM\Service\ListGen;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractListGenType
{
    protected $lg;
    protected $router;
    protected $em;
    protected $translator;
    protected $rep;
    protected $security;

    /**
     * AbstractListGenType constructor.
     */
    public function __construct(TranslatorInterface $translator, EntityManagerInterface $entityManager, UrlGeneratorInterface $router, Security $security)
    {
        $this->router = $router;
        $this->translator = $translator;
        $this->em = $entityManager;
        $this->security = $security;
    }

    /**
     * création d'une nouvelle instance de ListGen.
     */
    public function setDependancies()
    {
        $this->lg = new ListGen($this->translator, $this->em, $this->security);
    }

    //abstract public function getList();
}
