<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\Interlocutor;
use App\ESM\Entity\InterlocutorCenter;
use App\ESM\Form\InterlocutorType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class InterlocutorHandler extends AbstractFormHandler
{
    private $entityManager;

    public const jobNoRpps = [10, 13, 14];
    public const jobInv = [13, 14];

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return InterlocutorType::class;
    }

    /**
     * @param Interlocutor $data
     */
    protected function process($data): void
    {
        if (!in_array($data->getJob()->getId(), self::jobNoRpps)) {
            $data->setRppsNumber(str_repeat('0', 11));
        }

        if (UnitOfWork::STATE_NEW === $this->entityManager->getUnitOfWork()->getEntityState($data)) {
            $this->entityManager->persist($data);
        }

        $this->entityManager->flush();
    }

    /**
     * @param Interlocutor $data
     */
    public function handle(Request $request, $data, array $options = []): bool
    {
        $this->form = $this->formFactory->create($this->getFormType(), $data, $options)->handleRequest($request);

        if ($this->form->isSubmitted() && $this->form->isValid()) {

        	$isRPPSJob   = in_array($data->getJob()->getId(), self::jobNoRpps, true);
        	$isZeroCode  = $data->getRppsNumber() === str_repeat('0', 11);
        	$isInfirmier = $data->getJob()->getId() === 10;

        	// check doublon n° rpps pour la France (sauf infirmier)
            if ($isRPPSJob && !$isZeroCode && !$isInfirmier) {

				$rpps = $this->entityManager->getRepository(Interlocutor::class)->findOneBy(['rppsNumber' => $data->getRppsNumber()]);

				if (null !== $rpps && $rpps->getId() !== $data->getId()) {
					$this->form->get('rppsNumber')->addError(new FormError('Ce numéro est déjà utilisé'));

					return false;
				}
            }

            // check détach établissement présent dans centre actif
            $originalData = $this->entityManager->getUnitOfWork()->getOriginalEntityData($data);

            if (!empty($originalData)) {

                $institutionIds = array_map(function ($institution) {
                    return $institution->getId();
                }, $data->getInstitutions()->toArray());

                $interlocutorCenters = $this->entityManager->getRepository(InterlocutorCenter::class)->findByNotInInstitutions($data, $institutionIds);

                if (count($interlocutorCenters) > 0) {
                    $this->form->get('institutions')->addError(new FormError('Cet interlocuteur est rattaché à un centre via cet établissement: retrait d\'établissement non-autorisé'));

                    return false;
                }
            }

            $this->process($data);

            return true;
        }

        return false;
    }
}
