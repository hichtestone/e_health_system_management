<?php

namespace App\ESM\Validator;

use App\ESM\Entity\Institution;
use App\ESM\Entity\ReportBlock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UnitInstitutionValidator.
 */
class UnitInstitutionValidator extends ConstraintValidator
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
        if (!$constraint instanceof UnitInstitution) {
            throw new UnexpectedTypeException($constraint, UnitInstitution::class);
        }

        // Donnees du form
        $data = $this->requestStack->getCurrentRequest()->get('institution');

		if (!$data instanceof Institution) {
			if ('1' === $data['country']) {
				$finess = '' !== $data['finess'] ? $data['finess'] : '000000000';
				$siret = '' !== $data['siret'] ? $data['siret'] : '00000000000000';

				if ($finess === str_repeat('0', 9) && $siret === str_repeat('0', 14)) {
					$count = 0;
				} else {
					$count = $this->entityManager->getRepository(Institution::class)->countInstitution($data['idCurrent'], $finess);
				}

				$error = false;
				if (0 < $count) {
					$error = true;
				}
				if ($error) {
					$this->context->buildViolation($constraint->message)
						->addViolation();
				}
			}
		}  else {
			if (1 === $data->getCountry()->getId()) {

				if ($data->getFiness() === str_repeat('0', 9) && $data->getSiret() === str_repeat('0', 14)) {
					$count = 0;
				} else {
					$count = $this->entityManager->getRepository(Institution::class)->countInstitution($data->getId(), $data->getFiness());
				}

				$error = false;
				if (0 < $count) {
					$error = true;
				}
				if ($error) {
					$this->context->buildViolation($constraint->message)
						->addViolation();
				}
			}
		}

        return true;
    }
}
