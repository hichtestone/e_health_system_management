<?php

namespace App\ESM\Notification;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class CloseDemandNotification extends AbstractNotification
{
    public function notify(UserInterface $user, Request $request, string $subject, string $template, string $to, array $param)
    {
        parent::notify($user, $request, $subject, $template, $to, $param);
    }
}
