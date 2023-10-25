<?php

namespace App\ESM\Validator;

use App\ESM\Entity\Institution;
use App\ESM\Entity\ReportBlock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UnchangedInstitutionValidator.
 */
class UnchangedInstitutionValidator extends ConstraintValidator
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
        if (!$constraint instanceof UnchangedInstitution) {
            throw new UnexpectedTypeException($constraint, UnchangedInstitution::class);
        }

        // Donnees du form
        $data = $this->requestStack->getCurrentRequest()->get('institution');

		if ($data instanceof Institution) {
			$institution = $this->entityManager->getRepository(Institution::class)->find($data->getId());
			$country = $this->entityManager->getRepository(Institution::class)->getInstitution($data->getId());

			if (count($institution->getCenters()->toArray()) > 0 && $data->getCountry()->getId() !== (int) $country) {
				$this->context->buildViolation($constraint->message)
					->addViolation();
			}
		}

        return true;
    }
}
