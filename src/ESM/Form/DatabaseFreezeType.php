<?php

namespace App\ESM\Form;

use App\ESM\Entity\DropdownList\ProjectDatabaseFreezeReason;
use App\ESM\Entity\ProjectDatabaseFreeze;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DatabaseFreezeType
 * @package App\ESM\Form
 */
class DatabaseFreezeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('frozenAt', DateTimeType::class, [
                'label' => 'entity.Date.field.freezedAt',
                'attr' => [
                    'placeholder' => 'entity.Date.field.freezedAt',
                    'class' => 'js-datepicker isPublicFunding',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => true,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('reason', EntityType::class, [
                'label' => 'entity.Date.field.reason',
                'class' => ProjectDatabaseFreezeReason::class,
                'choice_label' => 'label',
            ])
            ->add('otherReason', TextType::class, [
                'label' => 'entity.Date.field.reason',
                'attr' => [
                    'placeholder' => 'entity.Date.field.reason',
                ],
                'empty_data' => '',
                'required' => false,
            ])
            ->add('reportDate', DateTimeType::class, [
                'label' => 'entity.Date.field.actualReportedAt',
                'attr' => [
                    'placeholder' => 'entity.Date.field.actualReportedAt',
                    'class' => 'js-datepicker isPublicFunding',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProjectDatabaseFreeze::class,
        ]);
    }
}
