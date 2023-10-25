<?php

declare(strict_types=1);

namespace App\ESM\Security\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

class AlreadyLoggedInException extends AccountStatusException
{
    public function getMessageKey()
    {
        return 'security.connexion.err.already_logged_in';
    }
}
