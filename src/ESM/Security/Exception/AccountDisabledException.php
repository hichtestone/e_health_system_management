<?php

declare(strict_types=1);

namespace App\ESM\Security\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * Class AccountDisabledException.
 */
class AccountDisabledException extends AccountStatusException
{
    public function getMessageKey()
    {
        return 'security.connexion.err.inactive_account';
    }
}
