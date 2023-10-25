<?php

namespace App\ESM\Validator;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class DelayReferenceDateValidator
 * @package App\Validator
 */
class DelayReferenceDateValidator extends ConstraintValidator
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): bool
    {
        if (!$constraint instanceof DelayReferenceDate) {
            throw new UnexpectedTypeException($constraint, DelayReferenceDate::class);
        }

        // Donnees du form
        $data = $this->requestStack->getCurrentRequest()->get('visit_setting');

        // delai approximatif
        $delay_approx = $data['delayApprox'];

        // Delai
        $delay = $data['delay'];

        $error = ("" === $delay_approx && "" === $delay && null === $value) || ("" !== $delay_approx && "" !== $delay && null !== $value);

        if (!$error) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }

        return true;
    }
}
