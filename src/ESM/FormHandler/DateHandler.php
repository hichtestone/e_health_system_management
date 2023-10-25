<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\Date;
use App\ESM\Form\DateType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class DateHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return DateType::class;
    }

    /**
     * @param $data
     */
    protected function process($data): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
     * @param Date $data
     */
    public function handle(Request $request, $data, array $options = []): bool
    {
        $this->form = $this->formFactory->create($this->getFormType(), $data, $options)->handleRequest($request);

        if ($this->form->isSubmitted() && $this->form->isValid()) {
            // mp: finalement warning non bloquant
            $hasError = false;
            /*if (null !== $data->getProject()->getStudyPopulation()) {
                if (1 === $data->getProject()->getStudyPopulation()->getId()) {
                    // pédiatrique
                    $m = 6;
                } else {
                    // adulte
                    $m = 12;
                }

                if (!$this->checkDates($data->getActualLPLVAt(), $data->getFinalReportAnalysedAt(), $m)) {
                    $this->form->get('finalReportAnalysedAt')->addError(new FormError("Cette date doit être dans les $m mois après la date réelle LPLV du CJP"));
                    $hasError = true;
                }
                if (!$this->checkDates($data->getFinalActualLPLVAt(), $data->getFinalActualReportAt(), $m)) {
                    $this->form->get('finalActualReportAt')->addError(new FormError("Cette date doit être dans les $m mois après la date réelle LPLV final"));
                    $hasError = true;
                }

                if (!$this->checkDates($data->getActualLPLVAt(), $data->getDepotClinicalTrialsAt(), $m)) {
                    $this->form->get('depotClinicalTrialsAt')->addError(new FormError("Cette date doit être dans les $m mois après la date réelle LPLV du CJP"));
                    $hasError = true;
                }
                if (!$this->checkDates($data->getFinalActualLPLVAt(), $data->getDepotEudraCtAt(), $m)) {
                    $this->form->get('depotEudraCtAt')->addError(new FormError("Cette date doit être dans les $m mois après la date réelle LPLV final"));
                    $hasError = true;
                }
            }
            if ($hasError) {
                return false;
            }*/

            $this->process($data);

            return true;
        }

        return false;
    }

    private function checkDates(?DateTime $d1, ?DateTime $d2, int $delay): bool
    {
        if (null !== $d1 && null !== $d2) {
            $diff = date_diff($d1, $d2);

            return $diff->format('%m') > $delay || $d1 < $d2;
        }

        return true;
    }
}
