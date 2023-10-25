<?php

declare(strict_types=1);

namespace App\ESM\EventSubscriber\Security;

use App\ESM\Entity\User;
use App\ESM\Service\SingleSignOn\SSOService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * Init device session
 * Class CFSSORedirectSubscriber.
 */
class CFSSORedirectSubscriber implements EventSubscriberInterface
{
    /** @var string */
    private const FIREWALL_NAME = 'main';

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var RouterInterface */
    private $router;

    /**
     * CFSSORedirectSubscriber constructor.
     */
    public function __construct(TokenStorageInterface $tokenStorage, RouterInterface $router)
    {
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', -25]],
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

        // if connected
        $currentToken = $this->tokenStorage->getToken();

        if ($currentToken instanceof PostAuthenticationGuardToken) {
            /** @var User $user */
            $user = $currentToken->getUser();
            /** @var Session $session */
            $session = $event->getRequest()->getSession();

            // if authenticated and not already device session
            if (null !== $user
                && self::FIREWALL_NAME === $currentToken->getProviderKey()
                && $session->has(SSOService::SESSION_SP_URL_KEY)
            ) {
                // redirect
                $url = $session->get(SSOService::SESSION_SP_URL_KEY);
                $session->remove(SSOService::SESSION_SP_URL_KEY);
                $event->setResponse(new RedirectResponse($this->router->generate(SSOService::ROUTE_SET_TOKEN, ['url' => $url])));
            }
        }
    }
}
