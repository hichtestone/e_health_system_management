<?php

declare(strict_types=1);

namespace App\ESM\EventSubscriber\Security;

use App\ESM\Entity\User;
use App\ESM\Service\DeviceSession\DeviceSession;
use App\ESM\Service\OTP\OTPService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * Init device session
 * Class DeviceSessionSubscriber.
 */
class DeviceSessionSubscriber implements EventSubscriberInterface
{
    /** @var string */
    private const FIREWALL_NAME = 'main';

    /** @var TokenStorageInterface */
    private $tokenStorage;
    /** @var DeviceSession */
    private $deviceSession;

    /**
     * ExpiredSessionSubscriber constructor.
     */
    public function __construct(TokenStorageInterface $tokenStorage, DeviceSession $deviceSession)
    {
        $this->tokenStorage = $tokenStorage;
        $this->deviceSession = $deviceSession;
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', -10]],
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
            if (self::FIREWALL_NAME === $currentToken->getProviderKey()
                && !$session->has(DeviceSession::SESSION_TOKEN_KEY)
                && in_array(OTPService::ROLE_TWO_FACTOR_SUCCEED, $currentToken->getAttributes()['cust_roles'] ?? [], true)) {
                // start device session
                $this->deviceSession->createSession($user);
            }
        }
    }
}
