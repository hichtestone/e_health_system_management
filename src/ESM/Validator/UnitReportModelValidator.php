<?php

namespace App\ESM\Validator;

use App\ESM\Entity\ReportModel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UnitReportModelValidator.
 */
class UnitReportModelValidator extends ConstraintValidator
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
     * @param Constraint $constraint
     * @return bool
     */
    public function validate($value, Constraint $constraint): bool
    {
        if (!$constraint instanceof UnitReportModel) {
            throw new UnexpectedTypeException($constraint, UnitReportModel::class);
        }

        // Donnees du form
        $data = $this->requestStack->getCurrentRequest()->get('report_model');
        if (!empty($data['idCurrent'])) {
            $count = $this->entityManager->getRepository(ReportModel::class)->countModel($data['name'], (int) $data['idCurrent']);
        } else {
            $count = $this->entityManager->getRepository(ReportModel::class)->countModel($data['name'], '');

        }

        $error = false;
        if (0 < $count) {
            $error = true;
        }

        if ($error) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }

        return true;
    }
}
