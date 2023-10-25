<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\DocumentTransverse;
use App\ESM\Entity\VersionDocumentTransverse;
use App\ESM\Form\DocumentTransverseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class DocumentTransverseHandler extends AbstractFormHandler
{
    private $entityManager;
    private $params;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag)
    {
        $this->entityManager = $entityManager;
        $this->parameterBag = $parameterBag;
    }

    protected function getFormType(): string
    {
        return DocumentTransverseType::class;
    }

    /**
     * @param DocumentTransverse $data
     */
    protected function process($data): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        // Version a la creation d'un doc transverse
        if (!empty($this->form->all()['drugFirstVersion']) && null != $data->getDrugFirstVersion()) {
            $toDay = (new \DateTime())->format('Y-m-d H:i:s');

            // Date de debut de validite
            $dateStarted = $data->getValidStartAt();

            // Nombre de jours de validite du type de document
            $numberDays = $data->getTypeDocument()->getDaysCountValid();

            // Date de fin de validite calculee
            $dateValidEnd = date('Y-m-d H:i:s', strtotime($dateStarted->format('Y-m-d H:i:s')."+ $numberDays days"));
            $dateValidEnd = \DateTime::createFromFormat('Y-m-d H:i:s', $dateValidEnd);

            // Statut de validite
            $is_date_valide = false;
            if ($toDay >= $dateStarted->format('Y-m-d H:i:s') && $toDay <= $dateValidEnd->format('Y-m-d H:i:s')) {
                $is_date_valide = true;
            }

            $is_fichier_existe = false;
            if (null != $data->getFilenameVich() || (null != $data->getFilename() && '' != $data->getFilename())) {
                $is_fichier_existe = true;
            }

            // Statut valide si date valide et fichier existant
            $status = $is_date_valide && $is_fichier_existe;

            $filesystem = new Filesystem();
            $version = new VersionDocumentTransverse();
            $version->setVersion($data->getDrugFirstVersion());
            $version->setValidStartAt($dateStarted);
            $version->setValidEndAt($dateValidEnd);
            $version->setDocumentTransverse($data);
            $version->setDocumentTransverse($data);
            if (null != $data->getFilenameVich()) {
                $version->setFilename1($data->getFilename());
                // copy files from "documentsTransverse" to "documentsTransverseVersion" folder
                $filesystem->copy(
                    $this->parameterBag->get('DOCUMENTS_PATH').'/documentsTransverse/'.$data->getFilename(),
                    $this->parameterBag->get('DOCUMENTS_PATH').'/documentsTransverseVersion/'.$data->getFilename()
                );
            }
            $data->addVersionDocumentTransverse($version);
            $version->setIsValid($status);

            $this->entityManager->persist($version);
            $this->entityManager->flush();
        }
    }

    /**
     * @param DocumentTransverse $data
     */
    public function handle(Request $request, $data, array $options = []): bool
    {
        $this->form = $this->formFactory->create($this->getFormType(), $data, $options)->handleRequest($request);

        if ($this->form->isSubmitted() && $this->form->isValid()) {
            // Verif unicite du document
            $documents = $this->entityManager->getRepository(DocumentTransverse::class)->getDocumentByEntity($this->form->get('name')->getData(), $data->getDrug(), $data->getInstitution(), $data->getInterlocutor());
            foreach ($documents as $document) {
                if (null !== $document && $document->getId() !== $data->getId()) {
                    $this->form->get('name')->addError(new FormError('document.name'));

                    return false;
                }
            }

            $toDay = (new \DateTime())->format('Y-m-d H:i:s');

            // Date de debut de validite
            $dateStarted = $data->getValidStartAt();

            // Nombre de jours de validite du type de document
            $numberDays = $data->getTypeDocument()->getDaysCountValid();

            // Date de fin de validite calculee
            $dateValidEnd = date('Y-m-d H:i:s', strtotime($dateStarted->format('Y-m-d H:i:s')."+ $numberDays days"));
            $dateValidEnd = \DateTime::createFromFormat('Y-m-d H:i:s', $dateValidEnd);

            // Update  Date de fin de validite
            $data->setValidEndAt($dateValidEnd);

            // Statut de validite
            $is_date_valide = false;
            if ($toDay >= $dateStarted->format('Y-m-d H:i:s') && $toDay <= $dateValidEnd->format('Y-m-d H:i:s')) {
                $is_date_valide = true;
            }

            $is_fichier_existe = false;
            if (null != $data->getFilenameVich() || (null != $data->getFilename() && '' != $data->getFilename())) {
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
