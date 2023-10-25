<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\Center;
use App\ESM\Entity\DropdownList\Country;
use App\ESM\Entity\Project;
use App\ESM\Entity\Submission;
use App\ESM\Form\ProjectType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class ProjectHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return ProjectType::class;
    }

    /**
     * @param Project $data
     */
    public function handle(Request $request, $data, array $options = []): bool
    {
        $this->form = $this->formFactory->create($this->getFormType(), $data, $options)->handleRequest($request);

        if ($this->form->isSubmitted() && $this->form->isValid()) {

            // check qu'on retire pas un pays utilisé
            $originalData = $this->entityManager->getUnitOfWork()->getOriginalEntityData($data);
            if (!empty($originalData)) {
                $countryIds = array_map(function ($country) {
                    return $country->getId();
                }, $data->getCountries()->toArray());

                $centers = $this->entityManager->getRepository(Center::class)
                    ->findByProjectNotInCountries($data, $countryIds);
                if (count($centers) > 0) {
                    $this->form->get('countries')->addError(new FormError('Erreur : impossible de supprimer un pays contenant au moins un centre.'));

                    return false;
                }

                $submissions = $this->entityManager->getRepository(Submission::class)
                    ->findByProjectNotInCountries($data, $countryIds);
                if (count($submissions) > 0) {
                    $this->form->get('countries')->addError(new FormError('Erreur : impossible de supprimer un pays contenant au moins une soumission réglementaire centres.'));

                    return false;
                }
            }

            $this->process($data);

            return true;
        }

        return false;
    }

    /**
     * @param Project $data
     */
    protected function process($data): void
    {
        $data->setUpdatedAt(new \DateTime());

        // si sponsor Unicancer, pas de délégation possible
        if ('UNICANCER' === $data->getSponsor()->getLabel()) {
            $data->setDelegation(null);
        }

        if ('France' === $data->getTerritory()->getLabel()) {
            $fr = $this->entityManager->getRepository(Country::class)->findOneBy(['code' => 'FR']);
            if (!$data->hasCountry($fr)) {
                $data->addCountry($fr);
            }
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
