<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\InterlocutorCenter;
use App\ESM\Form\InterlocutorCenterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class InterlocutorCenterHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return InterlocutorCenterType::class;
    }

    /**
     * @param InterlocutorCenter $data
     */
    public function handle(Request $request, $data, array $options = []): bool
    {
        $this->form = $this->formFactory->create($this->getFormType(), $data, $options)->handleRequest($request);

        if ($this->form->isSubmitted() && $this->form->isValid()) {
            $originalData = $this->entityManager->getUnitOfWork()->getOriginalEntityData($data);
            if (!empty($originalData)) {
                if ($originalData['isPrincipalInvestigator'] && !$data->isPrincipalInvestigator()
                    && 0 === count($data->getCenter()->getPrincipalInvestigators())) {
                    $this->form->get('isPrincipalInvestigator')->addError(new FormError('Erreur : impossible de supprimer le dernier investigateur principal du centre.'));

                    return false;
                }
            }

            $this->process($data);

            return true;
        }

        return false;
    }

    /**
     * @param InterlocutorCenter $data
     */
    protected function process($data): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
