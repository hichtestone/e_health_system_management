<?php

namespace App\ESM\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class UnchangedInstitution.
 */
class UnchangedInstitution extends Constraint
{
    public $message = 'Impossible de modifier le pays d\'un établissement relié à au moins un centre actif.';
}
