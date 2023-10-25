<?php

declare(strict_types=1);

namespace App\ESM\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class ComplexPatternValidator.
 */
class ComplexPatternValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ComplexPattern) {
            throw new UnexpectedTypeException($constraint, ComplexPattern::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        // regex invalidantes
        foreach ($constraint->regexInvalid as $regex) {
            $test = preg_match('/'.$regex.'/', $value);

            if (false === $test) {
                $this->context->buildViolation('Invalid regex')
                    ->setInvalidValue($value)
                    ->setCode('Invalid regex')
                    ->addViolation()
                ;

                return;
            } elseif (0 !== $test) {
                $this->context->buildViolation($constraint->message)
                    ->setInvalidValue($value)
                    ->setCode(Regex::REGEX_FAILED_ERROR)
                    ->addViolation()
                ;

                return;
            }
        }

        // regex validantes
        foreach ($constraint->regexValid as $regex) {
            $test = preg_match('/'.$regex.'/', $value);

            if (false === $test) {
                $this->context->buildViolation('Invalid regex')
                    ->setInvalidValue($value)
                    ->setCode('Invalid regex')
                    ->addViolation()
                ;

                return;
            } elseif (0 === $test) {
                $this->context->buildViolation($constraint->message)
                    ->setInvalidValue($value)
                    ->setCode(Regex::REGEX_FAILED_ERROR)
                    ->addViolation()
                ;

                return;
            }
        }
    }
}
