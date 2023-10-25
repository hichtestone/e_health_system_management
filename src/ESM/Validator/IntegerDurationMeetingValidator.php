<?php

namespace App\ESM\Validator;

use App\ESM\Entity\Meeting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class IntegerDurationMeetingValidator.
 */
class IntegerDurationMeetingValidator extends ConstraintValidator
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
    }

	/**
	 * @param mixed $value
	 * @param Constraint $constraint
	 * @return bool
	 */
    public function validate($value, Constraint $constraint): bool
    {
        if (!$constraint instanceof IntegerDurationMeeting) {
            throw new UnexpectedTypeException($constraint, IntegerDurationMeeting::class);
        }

		// Donnees du form
		$data = $this->requestStack->getCurrentRequest()->get('meeting');

		if ($data instanceof Meeting) {

			$duration = $data->getDuration();

			if (strpos($duration, ':') !== 2) {
				$this->context->buildViolation($constraint->message)->addViolation();

			} elseif (strlen($duration) !== 5) {
				$this->context->buildViolation($constraint->message)->addViolation();

			} elseif (!is_numeric($duration[0]) || !is_numeric($duration[1]) || !is_numeric($duration[3]) || !is_numeric($duration[4])) {
				$this->context->buildViolation($constraint->message)->addViolation();

			} elseif (substr($duration, 0, 2) > 23) {
				$this->context->buildViolation($constraint->message)->addViolation();

			} elseif (substr($duration, 3, 2) > 59) {
				$this->context->buildViolation($constraint->message)->addViolation();
			}

		} elseif (is_array($data)) {

			$duration = $data['duration'];

			if (strpos($duration, ':') !== 2) {
				$this->context->buildViolation($constraint->message)->addViolation();

			} elseif (strlen($duration) !== 5) {
				$this->context->buildViolation($constraint->message)->addViolation();

			} elseif (!is_numeric($duration[0]) || !is_numeric($duration[1]) || !is_numeric($duration[3]) || !is_numeric($duration[4])) {
				$this->context->buildViolation($constraint->message)->addViolation();

			} elseif (substr($duration, 0, 2) > 23) {
				$this->context->buildViolation($constraint->message)->addViolation();

			} elseif (substr($duration, 3, 2) > 59) {
				$this->context->buildViolation($constraint->message)->addViolation();
			}
		}

		return true;
    }
}
