<?php

namespace App\ESM\Form;

use App\ESM\Entity\DropdownList\CallProject;
use App\ESM\Entity\DropdownList\Devise;
use App\ESM\Entity\DropdownList\Funder;
use App\ESM\Entity\Funding;
use App\ESM\Form\BaseType\BoolType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FundingType
 * @package App\ESM\Form
 */
class FundingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('publicFunding', BoolType::class, [
                'label'    => 'entity.Funding.field.publicFunding',
                'required' => false,
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'entity.Funding.field.comment',
                'attr'  => [
                    'placeholder' => 'entity.Funding.placeholders.comment',
                ],
                'required' => false,
            ])
            ->add('obtainedAt', DateTimeType::class, [
                'label' => 'entity.Funding.field.obtainedAt',
                'attr' => [
                    'placeholder' => 'entity.Funding.placeholders.obtainedAt',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => true,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('amount', NumberType::class, [
                'label' => 'entity.Funding.field.amount',
                'attr' => [
                    'placeholder' => 'entity.Funding.placeholders.amount',
                ],
                'required' => true,
            ])
            ->add('demandedAt', DateTimeType::class, [
                'label' => 'entity.Funding.field.demandedAt',
                'attr' => [
                    'placeholder' => 'entity.Funding.placeholders.demandedAt',
                    'class' => 'js-datepicker isPublicFunding',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => true,
                'format' => 'dd/MM/yyyy',
                'by_reference' => true,
            ])
            ->add('callProject', EntityType::class, [
                'label' => 'entity.Funding.field.callProject',
                'class' => CallProject::class,
                'attr' => [
					'class' => 'isPublicFunding',
                ],
				'placeholder' => 'entity.Funding.field.callProject',
				'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.label', 'ASC');
                },
                'choice_label' => 'label',
                'by_reference' => true,
            ])
            ->add('devise', EntityType::class, [
                'label' => 'entity.Funding.field.devise',
                'class' => Devise::class,
                'choice_label' => 'name',
            ])
            ->add('funder', EntityType::class, [
                'label' => 'entity.Funding.field.funder',
                'class' => Funder::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('f')
                        ->orderBy('f.label', 'ASC');
                },
                'choice_label' => 'label',
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Funding::class,
        ]);
    }
}
