<?php

declare(strict_types=1);

namespace App\ETMF\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ComplexPattern extends Constraint
{
    /** @var string */
    public $message = 'This value doesn\'t match the required pattern';
    /** @var array */
    public $regexValid = [];
    /** @var array */
    public $regexInvalid = [];
}
