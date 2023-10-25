<?php

namespace App\ESM\Notification;

use App\ESM\Service\Mailer\MailerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class AbstractNotification implements NotificationInterface
{
    private $mailerService;

    /**
     * @required
     */
    public function setFormFactory(MailerService $mailerService): void
    {
        $this->mailerService = $mailerService;
    }

    public function notify(UserInterface $user, Request $request, string $subject, string $template, string $to, array $param)
    {
        $this->mailerService->setLocale($user->getLocale());
        $this->mailerService->sendEmail($template, $param, $subject, [$user->getEmail()], [$to]);
        $this->mailerService->resetLocale();
    }
}
