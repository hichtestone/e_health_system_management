<?php

declare(strict_types=1);

namespace App\ESM\EventSubscriber\Security;

use App\ESM\Entity\User;
use App\ESM\Service\DeviceSession\DeviceSession;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * Check if local session and redis synchro
 * Class ExpiredDeviceSessionSubscriber.
 */
class ExpiredDeviceSessionSubscriber implements EventSubscriberInterface
{
    /** @var string */
    private const FIREWALL_NAME = 'main';

    /** @var RouterInterface */
    private $router;
    /** @var TokenStorageInterface */
    private $tokenStorage;
    /** @var DeviceSession */
    private $deviceSession;

    /**
     * ExpiredSessionSubscriber constructor.
     */
    public function __construct(TokenStorageInterface $tokenStorage, RouterInterface $router, DeviceSession $deviceSession)
    {
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
        $this->deviceSession = $deviceSession;
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', -12]],
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
            // on exclus ping
            if (in_array($event->getRequest()->attributes->get('_route'), ['ping'], true)) {
                return;
            }

            /** @var User $user */
            $user = $currentToken->getUser();
            /** @var Session $session */
            $session = $event->getRequest()->getSession();

            // if authenticated with device session and not already expired shared session ok
            if (self::FIREWALL_NAME === $currentToken->getProviderKey()
                && $session->has(DeviceSession::SESSION_TOKEN_KEY)) {
                // si session redis existe pas => timeout ou que session en base ne correspond pas (=session php expiration trop long par rapport socket)
                $connexion = $this->deviceSession->getConnexion($session->get(DeviceSession::SESSION_TOKEN_KEY));
                if (null === $connexion || $this->deviceSession->isExpiredConnexion($connexion)) {
                    // si anomalie on kill la session
                    $this->deviceSession->destroy($session->get(DeviceSession::SESSION_TOKEN_KEY), 2);
                    $this->tokenStorage->setToken(null);
                    $session->invalidate();
                    $session->getFlashBag()->add('info', 'security.info.expired_session');
                    $response = new RedirectResponse($this->router->generate('login'));
                    $event->setResponse($response);

                    return;
                } else {
                    $this->deviceSession->refreshTimeout($connexion);
                }
            }
        }
    }
}
