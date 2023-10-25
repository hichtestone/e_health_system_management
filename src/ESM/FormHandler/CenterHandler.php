<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\Center;
use App\ESM\Form\CenterType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class CenterHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return CenterType::class;
    }

    /**
     * @param Center $data
     */
    protected function process($data): void
    {
        if (UnitOfWork::STATE_NEW === $this->entityManager->getUnitOfWork()->getEntityState($data)) {
            $this->entityManager->persist($data);
        }
        $this->entityManager->flush();
    }

    /**
     * @param Center $data
     */
    public function handle(Request $request, $data, array $options = []): bool
    {
        $this->form = $this->formFactory->create($this->getFormType(), $data, $options)->handleRequest($request);

        if ($this->form->isSubmitted() && $this->form->isValid()) {
            // check a least one PI
            $i = count($data->getPrincipalInvestigators());
            if (0 === $i && $data->getCenterStatus()->getType() >= 2) {
                $this->form->get('centerStatus')->addError(new FormError('Veuillez définir au moins un investigateur principal pour sélectionner ce statut.'));

                return false;
            }

            // check detached institutions have no active interlocutor
            $interlocutorCenters = $data->getInterlocutorCenters();
            foreach ($interlocutorCenters as $interlocutorCenter) {
                if (null === $interlocutorCenter->getDisabledAt()
                    && !$data->getInstitutions()->contains($interlocutorCenter->getService()->getInstitution())) {
                    $this->form->get('institutions')->addError(new FormError('Erreur : Vous ne pouvez détacher un établissement qui a au moins un interlocuteur lié dans ce centre.'));

                    return false;
                }
            }

            $this->process($data);

            return true;
        }

        return false;
    }
}
