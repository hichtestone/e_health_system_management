<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\Center;
use App\ESM\Entity\PatientVariable;
use App\ESM\Entity\SchemaCondition;
use App\ESM\Form\SchemaConditionType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\HttpFoundation\Request;

class SchemaConditionHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return SchemaConditionType::class;
    }

    /**
     * @param Center $data
     */
    public function handle(Request $request, $data, array $options = []): bool
    {
        $this->form = $this->formFactory->create($this->getFormType(), $data, $options)->handleRequest($request);


        if ($this->form->isSubmitted() && $this->form->isValid()) {

            $variablesList = explode(',',  $this->form['variable']->getData());


            // Add patient_variables if not exists
            if (!empty($variablesList)) {

                // remove variable not inside form
                if (!empty($data->getPatientVariable())) {
                    foreach ($data->getPatientVariable() as $patient_variable) {
                        if (!in_array($patient_variable->getId(), $variablesList)) {
                            $data->removePatientVariable($patient_variable);
                        }
                    }
                }


                foreach ($variablesList as $variable) {

                    /** @var PatientVariable $patient_variable */
                    $patient_variable = $this->entityManager->getRepository(PatientVariable::class)->find((int) $variable);

                    if (null != $patient_variable) {
                        $data->addPatientVariable($patient_variable);
                    }
                }
            }

            $this->process($data);

            return true;
        }

        return false;
    }

    /**
     * @param SchemaCondition $data
     */
    protected function process($data): void
    {
        if (UnitOfWork::STATE_NEW === $this->entityManager->getUnitOfWork()->getEntityState($data)) {
            $this->entityManager->persist($data);
        }
        $this->entityManager->flush();
    }
}
