<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\VersionDocumentTransverse;
use App\ESM\Form\VersionDocumentTransverseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class VersionDocumentTransverseHandler extends AbstractFormHandler
{
    private $entityManager;
    private $params;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return VersionDocumentTransverseType::class;
    }

    /**
     * @param VersionDocumentTransverse $data
     */
    protected function process($data): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
     * @param VersionDocumentTransverse $data
     */
    public function handle(Request $request, $data, array $options = []): bool
    {
        $options = [];
        $this->form = $this->formFactory->create($this->getFormType(), $data, $options)->handleRequest($request);

        if ($this->form->isSubmitted() && $this->form->isValid()) {
            $versions = $this->entityManager->getRepository(VersionDocumentTransverse::class)->findOneBySomeField($this->form->get('version')->getData(), $data->getDocumentTransverse());
            foreach ($versions as $vers) {
                if (null !== $vers && $vers->getId() !== $data->getId()) {
                    $this->form->get('version')->addError(new FormError('version.name'));

                    return false;
                }
            }
            if (strlen($this->form->get('version')->getData()) > 10) {
                $this->form->get('version')->addError(new FormError('version.bad_length'));

                return false;
            }
            // Date de debut de validite
            $dateStarted = $data->getValidStartAt();

            // Nombre de jours de validite du type de document
            $numberDays = $data->getDocumentTransverse()->getTypeDocument()->getDaysCountValid();

            // Date de fin de validite calculee
            $dateValidEnd = date('Y-m-d H:i:s', strtotime($dateStarted->format('Y-m-d H:i:s')."+ $numberDays days"));
            $dateValidEnd = \DateTime::createFromFormat('Y-m-d H:i:s', $dateValidEnd);

            // Update  Date de fin de validite
            $data->setValidEndAt($dateValidEnd);

            $toDay = (new \DateTime())->format('Y-m-d H:i:s');
            // Statut de validite
            $is_date_valide = false;
            if ($toDay >= $dateStarted->format('Y-m-d H:i:s') && $toDay <= $dateValidEnd->format('Y-m-d H:i:s')) {
                $is_date_valide = true;
            }

            $is_fichier_existe = false;
            if (null != $data->getFilename1Vich() || (null != $data->getFilename1() && '' != $data->getFilename1())) {
                $is_fichier_existe = true;
            }

            // Statut valide si date valide et fichier existant
            $status = $is_date_valide && $is_fichier_existe;

            $data->setIsValid($status);

            $this->process($data);

            return true;
        }

        return false;
    }
}
