<?php

declare(strict_types=1);

namespace App\ESM\Security\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * Class AccountLockedException.
 */
class AccountLockedException extends AccountStatusException
{
    public function getMessageKey()
    {
        return 'security.connexion.err.account_locked';
    }
}
