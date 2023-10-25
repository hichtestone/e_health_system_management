<?php

namespace App\ESM\Form;

use App\ESM\Entity\CourbeSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CourbeType
 * @package App\ESM\Form
 */
class CourbeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startAt', DateTimeType::class, [
                'label' => 'entity.CourbeSetting.field.inclusionAt',
                'attr' => [
                    'placeholder' => 'dd/mm/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => true,
                'format' => 'dd/MM/yyyy',
            ])

        ;
        $builder->add('unit', ChoiceType::class, [
            'label' => 'entity.CourbeSetting.field.unit',
            'choices' => [
                'entity.CourbeSetting.choice.semaine' => 'semaine',
                 'entity.CourbeSetting.choice.mois' => 'mois',
            ],
        ]);

        $builder->add('points', CollectionType::class, [
            'entry_type' => PointType::class,
            'entry_options' => ['label' => false],
            'allow_add' => true,
            'by_reference' => false,
            'allow_delete' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => CourbeSetting::class,
            ])
            ->setRequired('project');
    }
}
