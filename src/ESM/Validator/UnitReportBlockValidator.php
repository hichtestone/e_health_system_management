<?php

namespace App\ESM\Validator;

use App\ESM\Entity\ReportBlock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UnitReportModelBlockValidator.
 */
class UnitReportBlockValidator extends ConstraintValidator
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
        if (!$constraint instanceof UnitReportBlock) {
            throw new UnexpectedTypeException($constraint, UnitReportBlock::class);
        }

        // Donnees du form
        $data = $this->requestStack->getCurrentRequest()->get('report_block');
        if (!empty($data['idCurrent'])) {
            $count = $this->entityManager->getRepository(ReportBlock::class)->countBlock($data['reportModelVersion'], $data['name'], (int) $data['idCurrent']);
        } else {
            $count = $this->entityManager->getRepository(ReportBlock::class)->countBlock($data['reportModelVersion'], $data['name'], '');
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
