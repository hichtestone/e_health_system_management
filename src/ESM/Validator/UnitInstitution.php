<?php

namespace App\ESM\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class UnitInstitution.
 */
class UnitInstitution extends Constraint
{
	// check doublon n° siret/finess pour la France
    public $message = 'Le numéro Finess existe déjà';
}
