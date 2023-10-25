<?php

namespace App\ESM\Form\EventSubscriber;

use App\ESM\Entity\DocumentTransverse;
use App\ESM\Entity\Drug;
use App\ESM\Entity\Institution;
use App\ESM\Entity\Interlocutor;
use App\ESM\Entity\PorteeDocumentTransverse;
use App\ESM\Entity\TypeDocumentTransverse;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichFileType;

/**
 * Class DocumentTypeSubscriber
 * @package App\ESM\Form\EventSubscriber
 */
class DocumentTypeSubscriber implements EventSubscriberInterface
{
    private $requestStack;
    /**
     * @var string
     */
    private $route;

    public function __construct(string $route)
    {
        $this->route = $route;
    }

    /**
     * @return array|string[]
     */
    public static function getSubscribedEvents(): array
	{
        return [FormEvents::PRE_SET_DATA => 'presetData'];
    }

	/**
	 * @param FormEvent $event
	 */
    public function presetData(FormEvent $event): void
    {
        /** @var DocumentTransverse $entity */
        $entity = $event->getData();

        $form = $event->getForm();

        $is_required = false;

        switch ($this->route) {
            // Depuis General
            case 'document_transverse.edit':
                $this->addValidatedAt($form);
                $this->addFile($form, $is_required);
                $this->addGeneralFields($form, $entity);
                break;

            // Depuis medicament creation
            case 'admin.drug.document.new':
                $this->addValidatedAt($form);
                $this->addFile($form, $is_required);
                $this->addDrugFields($form);
                $form
                    ->add('drugFirstVersion', TextType::class, [
                        'label' => 'entity.VersionDocumentTransverse.field.version',
                        'attr' => [
                            'regex:/^[a-zA-Z0-9\s]+$/',
                        ],
                    ]);

                    // no break
            case 'document_transverse_drug.edit':
                $this->addDrugFields($form);
                break;

            // Depuis interlocuteur
            case 'document_transverse_interlocutor.edit':
            case 'admin.interlocutor.addTransverseDocument':
                $this->addValidatedAt($form);
                $this->addFile($form, $is_required);
                $this->addInterlocutorFields($form);
                break;

            // Depuis Etablissement
            case 'document_transverse_institution.edit':
            case 'admin.institution.addTransverseDocument':
                $this->addValidatedAt($form);
                $this->addFile($form, $is_required);
                $this->addInstitutionFields($form);
                break;
        }
    }

	/**
	 * @param FormInterface $form
	 */
    private function addDrugFields(FormInterface $form): void
    {
        // Depuis medicament
        $this->addTypesDocument($form, ['BI', 'RCP']);
    }

	/**
	 * @param FormInterface $form
	 */
    private function addInterlocutorFields(FormInterface $form): void
    {
        // Depuis Interlocutor
        $this->addTypesDocument($form, ['CV']);
    }

	/**
	 * @param FormInterface $form
	 */
    private function addInstitutionFields(FormInterface $form): void
    {
        // Depuis Institution
        $this->addTypesDocument($form, ['NORMES_LABORATOIRE']);
    }

	/**
	 * @param FormInterface $form
	 * @param DocumentTransverse $documentTransverse
	 */
    private function addGeneralFields(FormInterface $form, DocumentTransverse $documentTransverse): void
    {
        $param = [];

        // Depuis Medicament
        if (null != $documentTransverse->getDrug()) {
            $param = ['BI', 'RCP'];

            $form
                ->add('drug', EntityType::class, [
                    'label' => 'entity.DocumentTransverse.field.drug',
                    'class' => Drug::class,
                    'choice_label' => 'name',
                ]);
        }

        // Depuis Etablissement
        if (null != $documentTransverse->getInstitution()) {
            $param = ['NORMES_LABORATOIRE'];

            $form->add('institution', EntityType::class, [
                'label' => 'entity.DocumentTransverse.field.institution',
                'class' => Institution::class,
                'choice_label' => 'name',
            ]);
        }

        // Depuis Interlocuteur
        if (null != $documentTransverse->getInterlocutor()) {
            $param = ['CV'];

            $form->add('interlocutor', EntityType::class, [
                'label' => 'entity.DocumentTransverse.field.interlocutor',
                'class' => Interlocutor::class,
                'choice_label' => 'firstName',
                'attr' => [
                    'placeholder' => 'entity.DocumentTransverse.field.interlocutor',
                ],
                'mapped' => false,
            ]);
        }

        $this->addTypesDocument($form, $param);

        $form
            ->add('porteeDocument', EntityType::class, [
                'label' => 'entity.DocumentTransverse.field.porteeDocument',
                'class' => PorteeDocumentTransverse::class,
                'choice_label' => 'name',
            ]);
    }

	/**
	 * @param FormInterface $form
	 * @param array $codes
	 */
    private function addTypesDocument(FormInterface $form, array $codes): void
    {
        $form->add('TypeDocument', EntityType::class, [
            'label' => 'entity.DocumentTransverse.field.TypeDocument',
            'class' => TypeDocumentTransverse::class,
            'choice_label' => 'name',
            'query_builder' => function (EntityRepository $er) use ($codes) {
                return $er->createQueryBuilder('c')
                    ->andWhere('c.code IN(:code)')
                    ->setParameter('code', $codes);
            },
        ]);
    }

	/**
	 * @param FormInterface $form
	 * @param bool $is_required
	 */
    private function addFile(FormInterface $form, bool $is_required): void
    {
        $form->add('filenameVich', VichFileType::class, [
            'label' => 'entity.DocumentTransverse.field.Document',
            'required' => $is_required,
            'allow_delete' => true,
            'delete_label' => 'Supprimer',
            'download_link' => false,
            'asset_helper' => true,
            'constraints' => [
                new File([
                    'mimeTypes' => [
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'application/vnd.oasis.opendocument.text',
                        'application/vnd.oasis.opendocument.spreadsheet',
                        'application/vnd.oasis.opendocument.presentation',
                        'application/pdf',
                    ],
                    'mimeTypesMessage' => 'Les types des fichiers suivants sont autorisés : .xls, .xlsx, .doc, .docx, .ppt, .pptx, .odt, .ods, .odp, .pdf',
                ]),
            ],
        ]);
    }

	/**
	 * @param FormInterface $form
	 */
    private function addValidatedAt(FormInterface $form)
    {
        $form->add('validStartAt', DateTimeType::class, [
            'label' => 'entity.DocumentTransverse.field.validStartAt',
            'attr' => [
                'placeholder' => 'dd/mm/yyyy',
                'class' => 'js-datepicker',
                'autocomplete' => 'off',
            ],
            'html5' => false,
            'widget' => 'single_text',
            'required' => true,
            'format' => 'dd/MM/yyyy',
        ]);
    }
}
