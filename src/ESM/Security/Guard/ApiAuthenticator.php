<?php

declare(strict_types=1);

namespace App\ESM\Security\Guard;

use App\ESM\Entity\ConnectionAuditTrail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ApiAuthenticator.
 */
class ApiAuthenticator extends AbstractGuardAuthenticator
{
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var TranslatorInterface */
    private $translator;

    /**
     * MainAuthenticator constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * Does this guard has to been called ?
     */
    public function supports(Request $request): bool
    {
        return $request->headers->has('authorization');
    }

	/**
	 * Get credentials from request.
	 *
	 * @param Request $request
	 * @return array|null
	 */
    public function getCredentials(Request $request): ?array
	{
        $auth = $request->headers->get('authorization');
        $arr = explode(' ', $auth);
        $bearer = end($arr);
        $arr = explode('.', $bearer);
        if (2 !== count($arr)) {
            return null;
        } else {
            return [
                'api_token' => $arr[0],
                'device_session_token' => $arr[1],
            ];
        }
    }

	/**
	 * Try to get user.
	 *
	 * @param mixed $credentials
	 * @param UserProviderInterface $userProvider
	 * @return UserInterface|null
	 */
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
	{
        if (null === $credentials) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            return null;
        }

        // on check si session ouverte et on récupère le user
        /** @var ConnectionAuditTrail $device */
        $conn = $this->entityManager->getRepository(ConnectionAuditTrail::class)->findApiSession($credentials['api_token'], $credentials['device_session_token']);

        if (null === $conn) {
            throw new CustomUserMessageAuthenticationException('security.connexion.err.bad_api_token');
        }

        return $conn->getUser();
    }

    /**
     * Check credentials.
     *
     * @param mixed $credentials
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    /**
     * @param string $providerKey
     *
     * @return null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $data = [
            'message' => $this->translator->trans($exception->getMessageKey(), $exception->getMessageData()),
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     */
    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        $data = [
            'message' => 'Authentication Required',
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }
}
