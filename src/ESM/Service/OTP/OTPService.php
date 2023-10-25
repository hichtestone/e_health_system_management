<?php

declare(strict_types=1);

namespace App\ESM\Service\OTP;

use App\ESM\Entity\User;
use OTPHP\TOTP;
use OTPHP\TOTPInterface;
use ParagonIE\ConstantTime\Base32;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * Class OTPService.
 */
class OTPService
{
    public const IMG_URL = 'https://assets.gitlab-static.net/uploads/-/system/user/avatar/2876944/avatar.png';
    public const ROLE_TWO_FACTOR_SUCCEED = 'TWO_FACTOR_SUCCEED';
    public const ROUTE_TWO_FACTOR = 'two_factor';

    /** @var string */
    private $secret;

    /**
     * @throws \Exception
     */
    public function getUserOTP(User $user): TOTPInterface
    {
        if (null === $user->getTwoFactorSecret()) {
            return $this->generateNewOtp($user);
        } else {
            return $this->createOTP($user->getTwoFactorSecret(), $user);
        }
    }

    /**
     * @throws \Exception
     */
    public function generateNewOtp(User $user, ?string $secret = null): TOTPInterface
    {
        if (null === $secret) {
            $secret = $this->generateSecret();
        }

        return $this->createOTP($secret, $user);
    }

    private function createOTP(string $secret, User $user): TOTPInterface
    {
        $otp = TOTP::create($secret);
        $otp->setLabel($user->getUsername());
        $otp->setIssuer('Template');
        $otp->setParameter('image', self::IMG_URL);

        return $otp;
    }

    /**
     * @throws \Exception
     */
    public function generateSecret(): string
    {
        $this->secret = trim(Base32::encodeUpper(hash('sha256', random_bytes(64))), '=');

        return $this->secret;
    }

    /**
     * @throws \Exception
     */
    public function addTwoFactorRole(TokenStorageInterface $tokenStorage, SessionInterface $session): void
    {
        /** @var PostAuthenticationGuardToken $currentToken */
        $currentToken = $tokenStorage->getToken();
        $arr = $currentToken->getAttributes()['cust_roles'] ?? [];
        $arr = array_merge($arr, [self::ROLE_TWO_FACTOR_SUCCEED]);
        $currentToken->setAttribute('cust_roles', $arr);
        $tokenStorage->setToken($currentToken);
        $session->set('_security_'.$currentToken->getProviderKey(), serialize($currentToken));
    }
}
