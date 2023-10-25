<?php

namespace App\ESM\Validator;

use App\ESM\Entity\Exam;
use App\ESM\Entity\PatientVariable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UnitVariableExamValidator
 * @package App\Validator
 */
class UnitVariableExamValidator extends ConstraintValidator
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
        if (!$constraint instanceof UnitVariableExam) {
            throw new UnexpectedTypeException($constraint, UnitVariableExam::class);
        }

        // Donnees du form
        $data = $this->requestStack->getCurrentRequest()->get('exam');
		if ($data instanceof Exam) {
			$count = $this->entityManager->getRepository(PatientVariable::class)->countPatientVariable($data->getProject()->getId(), $data->getName());
			$countExam = $this->entityManager->getRepository(Exam::class)->getCountExam($data->getProject()->getId(), $data->getName());

			$error = false;
			if ($count !== $countExam) {
				$error = true;
			}

			if ($error) {
				$this->context->buildViolation($constraint->message)
					->addViolation();
			}
		} else {
			$count = $this->entityManager->getRepository(PatientVariable::class)->countPatientVariable($data['project'], $data['name']);
			$countExam = $this->entityManager->getRepository(Exam::class)->getCountExam($data['project'], $data['name']);

			$error = false;
			if ($count !== $countExam) {
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
