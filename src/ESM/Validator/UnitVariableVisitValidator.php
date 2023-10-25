<?php

namespace App\ESM\Validator;

use App\ESM\Entity\PatientVariable;
use App\ESM\Entity\PhaseSetting;
use App\ESM\Entity\Visit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UnitVariableVisitValidator
 * @package App\Validator
 */
class UnitVariableVisitValidator extends ConstraintValidator
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }

    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): bool
    {
        if (!$constraint instanceof UnitVariableVisit) {
            throw new UnexpectedTypeException($constraint, UnitVariableVisit::class);
        }

        // Donnees du form
        $data = $this->requestStack->getCurrentRequest()->get('visit_setting');

		if ($data instanceof Visit) {
			$count = $this->entityManager->getRepository(PatientVariable::class)->countPatientVariable($data->getProject()->getId(), $data->getShort());
			$countVisit = $this->entityManager->getRepository(Visit::class)->getCountVisit($data->getProject()->getId(), $data->getShort());

			$error = false;
			if ($count !== $countVisit) {
				$error = true;
			}

			if ($error) {
				$this->context->buildViolation($constraint->message)
					->addViolation();
			}
		} else {
			$count = $this->entityManager->getRepository(PatientVariable::class)->countPatientVariable($data['project'], $data['short']);
			$countVisit = $this->entityManager->getRepository(Visit::class)->getCountVisit($data['project'], $data['short']);

			$error = false;
			if ($count !== $countVisit) {
				$error = true;
			}

			if ($error) {
				$this->context->buildViolation($constraint->message)
					->addViolation();
			}
		}

        return true;
    }
}
