<?php

namespace App\ESM\Form\EventSubscriber;

use App\ESM\Entity\VersionDocumentTransverse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichFileType;

/**
 * Class VersionDocumentTypeSubscriber
 * @package App\ESM\Form\EventSubscriber
 */
class VersionDocumentTypeSubscriber implements EventSubscriberInterface
{
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
        /** @var VersionDocumentTransverse $entity */
        $form = $event->getForm();

        $is_required = false;

        $form->add('filename1Vich', VichFileType::class, [
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
}
