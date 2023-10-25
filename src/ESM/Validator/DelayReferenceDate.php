<?php

namespace App\ESM\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class DelayReferenceDate
 * @package App\Validator
 */
class DelayReferenceDate extends Constraint
{
    public $message = 'Entity.visit.reference_delay_date_error';
}
