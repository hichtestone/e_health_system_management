<?php

declare(strict_types=1);

namespace App\ESM\Security\Guard;

use App\ESM\Entity\ConnectionErrorAuditTrail;
use App\ESM\Entity\User;
use App\ESM\Repository\UserRepository;
use App\ESM\Service\SingleSignOn\SSOService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Class MainAuthenticator.
 */
class MainAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'login.check';

    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;
    /** @var User|null */
    private $user;
    /** @var Request */
    private $request;

    /**
     * MainAuthenticator constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->request = $requestStack->getCurrentRequest();
    }

	/**
	 * Does this guard has to been called ?
	 *
	 * @param Request $request
	 * @return bool
	 */
    public function supports(Request $request): bool
	{
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
               && $request->isMethod('POST');
    }

	/**
	 * Get credentials from request.
	 *
	 * @param Request $request
	 * @return array
	 */
    public function getCredentials(Request $request): array
	{
        $credentials = $request->request->get('login');
        if (($credentials[SSOService::SESSION_SP_URL_KEY] ?? '') !== '') {
            $request->getSession()->set(SSOService::SESSION_SP_URL_KEY, $credentials[SSOService::SESSION_SP_URL_KEY]);
        }
        $request->getSession()->set(Security::LAST_USERNAME, $credentials['email']);

        return $credentials;
    }

	/**
	 * Try to get user.
	 *
	 * @param mixed $credentials
	 *
	 * @return object|UserInterface|null
	 * @throws NonUniqueResultException
	 */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('login', $credentials['_token']);

        if (!$this->csrfTokenManager->isTokenValid($token)) {
            $msg = 'security.connexion.err.csrf_token';
            $this->logError(null, $msg, $credentials['email']);
            throw new InvalidCsrfTokenException($msg);
        }

        /** @var UserRepository $userRep */
        $userRep = $this->entityManager->getRepository(User::class);
        $user = $userRep->findByLogin($credentials['email']);

        if (null === $user) {
            // fail authentication with a custom error
            $msg = 'security.connexion.err.bad_credentials';
            $this->logError(null, $msg, $credentials['email']);
            throw new CustomUserMessageAuthenticationException($msg);
        }
        $this->user = $user;

        return $user;
    }

	/**
	 * Check credentials.
	 *
	 * @param mixed $credentials
	 * @param UserInterface $user
	 * @return bool
	 */
    public function checkCredentials($credentials, UserInterface $user): bool
	{
        if (!$this->passwordEncoder->isPasswordValid($user, $credentials['password'])) {
            $msg = 'security.connexion.err.bad_credentials';
            $this->logError($this->user, $msg);
            throw new CustomUserMessageAuthenticationException($msg);
        }

        return true;
    }

    /**
     * @param string $providerKey
     *
     * @return RedirectResponse|Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            if (false === strpos($targetPath, 'logout')) {
                return new RedirectResponse($targetPath);
            }
        }

        return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    /**
     * @return string
     */
    protected function getLoginUrl(): string
	{
        return $this->urlGenerator->generate('login');
    }

    /**
     * @param mixed $credentials
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    private function logError(?User $user, string $msg, ?string $username = null): void
    {
        $error = new ConnectionErrorAuditTrail();
        $error->setCreatedAt(new \DateTime());
        $error->setUser($user);
        $error->setError($msg);
        $error->setIp($this->request->getClientIp());
        $error->setDevice($this->request->headers->get('User-Agent'));
        $error->setUsername($username);
        $this->entityManager->persist($error);
        $this->entityManager->flush();
    }
}
