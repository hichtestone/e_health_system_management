<?php

namespace App\ESM\Service\ResetPasswordMail;

use App\ESM\Entity\User;
use App\ESM\Service\Mailer\MailerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ListGenService.
 */
class ResetPasswordMail
{
    /** @var MailerService */
    private $mailer;
    /** @var Request|null */
    private $request;

    /**
     * ResetPasswordMail constructor.
     */
    public function __construct(MailerService $mailer, RequestStack $requestStack)
    {
        $this->mailer = $mailer;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @return string
     */
    public function generateToken()
    {
        return sha1(base64_encode(md5(uniqid())));
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function sendWelcome(User $user): void
    {
        $this->mailer->setLocale($user->getLocale());
        $this->mailer->sendEmail('welcome', [
            'domain' => $this->request->getHttpHost(),
            'user' => $user,
        ], 'email.subject.welcome', [], [$user->getEmail()]);
        $this->mailer->resetLocale();
    }
}
