<?php

namespace App\ESM\Form;

use App\ESM\Entity\DropdownList\FormalityRule;
use App\ESM\Entity\DropdownList\RuleTransferTerritory;
use App\ESM\Entity\Rule;
use App\ESM\Form\BaseType\BoolType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RuleType
 * @package App\ESM\Form
 */
class RuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('formality', EntityType::class, [
                'label' => 'entity.Rule.field.formality',
                'class' => FormalityRule::class,
                'choice_label' => 'label',
            ])
            ->add('conformity', BoolType::class, [
                'label' => 'entity.Rule.field.conformity',
            ])
            ->add('studyTransfer', BoolType::class, [
                'label' => 'entity.Rule.field.studyTransfer',
            ])
            ->add('outStudyTransfer', BoolType::class, [
                'label' => 'entity.Rule.field.outStudyTransfer',
            ])
            ->add('post', BoolType::class, [
                'label' => 'entity.Rule.field.post',
            ])
            ->add('partner', TextType::class, [
                'label' => 'entity.Rule.field.partner',
                'attr' => [
                    'placeholder' => 'entity.Rule.field.partner',
                ],
                'required' => false,
            ])
            ->add('mapping', BoolType::class, [
                'label' => 'entity.Rule.field.mapping',
            ])
            ->add('reference', TextType::class, [
                'label' => 'entity.Rule.field.reference',
                'attr' => [
                    'placeholder' => 'entity.Rule.field.reference',
                ],
                'required' => true,
            ])
            ->add('validateMapping', BoolType::class, [
                'label' => 'entity.Rule.field.validateMapping',
            ])
            ->add('eTmf', TextType::class, [
                'label' => 'entity.Rule.field.eTmf',
                'attr' => [
                    'placeholder' => 'entity.Rule.field.eTmf',
                ],
                'required' => false,
            ])
            ->add('dataProtection', BoolType::class, [
                'label' => 'entity.Rule.field.dataProtection',
            ])
            ->add('dataAccess', BoolType::class, [
                'label' => 'entity.Rule.field.dataAccess',
            ])
            ->add('studyTransferTerritory', EntityType::class, [
                'label' => 'entity.Rule.field.study_transfer_territory',
                'class' => RuleTransferTerritory::class,
                'choice_label' => 'label',
                'required' => false,
            ])
            ->add('outTransferTerritory', EntityType::class, [
                'label' => 'entity.Rule.field.out_transfer_territory',
                'class' => RuleTransferTerritory::class,
                'choice_label' => 'label',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Rule::class,
        ]);
    }
}
