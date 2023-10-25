<?php

declare(strict_types=1);

namespace App\ESM\EventSubscriber;

use App\ESM\Entity\User;
use App\ESM\Service\OTP\OTPService;
use App\ESM\Service\TermsOfService\TOSService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * Class LocaleSubscriber.
 */
class TermsOfServiceSubscriber implements EventSubscriberInterface
{
    /** @var string */
    private const FIREWALL_NAME = 'main';

    /** @var RouterInterface */
    private $router;
    /** @var TokenStorageInterface */
    private $tokenStorage;
    /** @var TOSService */
    private $TOSService;

    /**
     * DoubleAuthSubscriber constructor.
     */
    public function __construct(TokenStorageInterface $tokenStorage, RouterInterface $router, TOSService $TOSService)
    {
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
        $this->TOSService = $TOSService;
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', -20]],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        // if already on TOS
        if (in_array($event->getRequest()->attributes->get('_route'), [TOSService::ROUTE_TOS, OTPService::ROUTE_TWO_FACTOR, 'ping'], true)) {
            return;
        }

        // if authenticated and not already TOS ok
        $currentToken = $this->tokenStorage->getToken();

        if ($currentToken instanceof PostAuthenticationGuardToken
           && self::FIREWALL_NAME === $currentToken->getProviderKey()
           && !in_array(TOSService::ROLE_TOS_SIGNED, $currentToken->getAttributes()['cust_roles'] ?? [], true)
        ) {
            /** @var User $currentUser */
            $currentUser = $currentToken->getUser();

            // if ok
            if ($this->TOSService->hasSignedLastTOS($currentUser)) {
                $this->TOSService->addTOSSignedRole($this->tokenStorage, $event->getRequest()->getSession());
            } else {
                // else redirect
                $response = new RedirectResponse($this->router->generate(TOSService::ROUTE_TOS));
                $event->setResponse($response);
            }
        }
    }
}
