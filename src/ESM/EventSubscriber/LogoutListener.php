<?php

declare(strict_types=1);

namespace App\ESM\EventSubscriber;

use App\ESM\Service\DeviceSession\DeviceSession;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

/**
 * Class LogoutListener.
 */
class LogoutListener implements LogoutSuccessHandlerInterface
{
    /** @var TokenStorageInterface */
    private $tokenStorage;
    /** @var RouterInterface */
    private $router;
    /** @var DeviceSession */
    private $deviceSession;

    public function __construct(TokenStorageInterface $tokenStorage, RouterInterface $router, DeviceSession $deviceSession)
    {
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->deviceSession = $deviceSession;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|void
     *
     * @throws \Exception
     */
    public function onLogoutSuccess(Request $request)
    {
        $currentToken = $this->tokenStorage->getToken();

        if ($currentToken instanceof PostAuthenticationGuardToken) {
            if ($request->getSession()->has(DeviceSession::SESSION_TOKEN_KEY)) {
                $this->deviceSession->destroy($request->getSession()->get(DeviceSession::SESSION_TOKEN_KEY), 1);
            } else {
                $request->getSession()->invalidate();
            }
        }

        return new RedirectResponse($this->router->generate('login'));
    }
}
