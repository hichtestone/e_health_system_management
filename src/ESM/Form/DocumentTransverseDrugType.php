<?php

namespace App\ESM\Form;

use App\ESM\Entity\DocumentTransverse;
use App\ESM\Entity\PorteeDocumentTransverse;
use App\ESM\Entity\TypeDocumentTransverse;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichFileType;

/**
 * Class DocumentTransverseDrugType
 * @package App\ESM\Form
 */
class DocumentTransverseDrugType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'entity.DocumentTransverse.field.name',
                'attr' => [
                    'placeholder' => 'entity.DocumentTransverse.field.name',
                ],
            ])
            ->add('TypeDocument', EntityType::class, [
                'label' => 'entity.DocumentTransverse.field.TypeDocument',
                'class' => TypeDocumentTransverse::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->andWhere('c.name = :name')
                        ->orWhere('c.name = :name1')
                    ->setParameters([
                        'name' => 'Bi',
                        'name1' => 'RCP',
                    ]);
                },
            ])

            ->add('porteeDocument', EntityType::class, [
                'label' => 'entity.DocumentTransverse.field.porteeDocument',
                'class' => PorteeDocumentTransverse::class,
               'choice_label' => 'name',
               'query_builder' => function (EntityRepository $er) {
                   return $er->createQueryBuilder('p')

                       ->where('p.name = :Medicament')
                       ->setParameter('Medicament', 'Medicament')
                  ;
               },
           ])

            ->add('validStartAt', DateTimeType::class, [
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
            ])

            ->add('filename1Vich', VichFileType::class, [
                'label' => 'entity.DocumentTransverse.field.Document',
                'required' => false,
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

        $builder->add('versionDocumentTransverses', CollectionType::class, [
            'entry_type' => VersionDocumentTransverseType::class,
            'entry_options' => ['label' => false],
            'allow_add' => true,
            'by_reference' => true,
            'allow_delete' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DocumentTransverse::class,
        ]);
    }
}
