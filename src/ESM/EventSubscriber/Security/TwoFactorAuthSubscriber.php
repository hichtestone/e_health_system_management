<?php

declare(strict_types=1);

namespace App\ESM\EventSubscriber\Security;

use App\ESM\Entity\User;
use App\ESM\Service\OTP\OTPService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * On redirige vers le formulaire de double auth si pas déjà fait
 * Class DoubleAuthSubscriber.
 */
class TwoFactorAuthSubscriber implements EventSubscriberInterface
{
    private const FIREWALL_NAME = 'main';

    /** @var RouterInterface */
    private $router;
    /** @var TokenStorageInterface */
    private $tokenStorage;
    /** @var OTPService */
    private $OTPService;

    /**
     * DoubleAuthSubscriber constructor.
     */
    public function __construct(TokenStorageInterface $tokenStorage, RouterInterface $router, OTPService $OTPService)
    {
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
        $this->OTPService = $OTPService;
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', -5]],
        ];
    }

    /**
     * @throws \Exception
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        // if already on two factor form
        if (OTPService::ROUTE_TWO_FACTOR === $event->getRequest()->attributes->get('_route')) {
            return;
        }

        // if authenticated and not already two factor ok
        $currentToken = $this->tokenStorage->getToken();

        if ($currentToken instanceof PostAuthenticationGuardToken
            && self::FIREWALL_NAME === $currentToken->getProviderKey()
            && !in_array(OTPService::ROLE_TWO_FACTOR_SUCCEED, $currentToken->getAttributes()['cust_roles'] ?? [], true)
        ) {
            /** @var User $user */
            $user = $currentToken->getUser();

            // if two factor not set
            if (null === $user->getTwoFactorSecret()) {
                $this->OTPService->addTwoFactorRole($this->tokenStorage, $event->getRequest()->getSession());
            } else {
                // else redirect
                $response = new RedirectResponse($this->router->generate(OTPService::ROUTE_TWO_FACTOR));
                $event->setResponse($response);
            }
        }
    }
}
