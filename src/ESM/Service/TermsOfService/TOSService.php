<?php

declare(strict_types=1);

namespace App\ESM\Service\TermsOfService;

use App\ESM\Entity\TermsOfService;
use App\ESM\Entity\TermsOfServiceSignature;
use App\ESM\Entity\User;
use App\ESM\Repository\TermsOfServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class TOSService
{
    public const ROLE_TOS_SIGNED = 'TOS_SIGNED';
    public const ROUTE_TOS = 'tos';

    /** @var EntityManagerInterface */
    private $em;

    /**
     * TOSService constructor.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getLastTOS(): TermsOfService
    {
        /** @var TermsOfServiceRepository $repTOS */
        $repTOS = $this->em->getRepository(TermsOfService::class);

        return $repTOS->findLast();
    }

    public function hasSignedLastTOS(User $user): bool
    {
        $lastTOS = $this->getLastTOS();
        $signature = $this->em->getRepository(TermsOfServiceSignature::class)
            ->findOneBy(['user' => $user, 'terms_of_service' => $lastTOS])
        ;

        return null !== $signature;
    }

    public function addTOSSignedRole(TokenStorageInterface $tokenStorage, SessionInterface $session): void
    {
        /** @var PostAuthenticationGuardToken $currentToken */
        $currentToken = $tokenStorage->getToken();
        $arr = $currentToken->getAttributes()['cust_roles'] ?? [];
        $arr = array_merge($arr, [self::ROLE_TOS_SIGNED]);
        $currentToken->setAttribute('cust_roles', $arr);
        $tokenStorage->setToken($currentToken);
        $session->set('_security_'.$currentToken->getProviderKey(), serialize($currentToken));
    }
}
