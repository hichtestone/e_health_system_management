<?php

namespace App\ESM\Form;

use App\ESM\Entity\VersionDocumentTransverse;
use App\ESM\Form\EventSubscriber\VersionDocumentTypeSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class VersionDocumentTransverseType
 * @package App\ESM\Form
 */
class VersionDocumentTransverseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // File attach version
        $builder->addEventSubscriber(new VersionDocumentTypeSubscriber());

        $builder
            ->add('version', TextType::class, [
                'attr' => [
                    'placeholder' => 'entity.VersionDocumentTransverse.field.version',
                    'regex:/^[a-zA-Z0-9\s]+$/',
                ],
            ])
            ->add('validStartAt', DateTimeType::class, [
                'label' => 'entity.VersionDocumentTransverse.field.validStartAt',
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
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => VersionDocumentTransverse::class,

        ]);
    }
}
