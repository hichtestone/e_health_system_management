<?php

namespace App\ESM\Form;

use App\ESM\Entity\PhaseSetting;
use App\ESM\Entity\SchemaCondition;
use App\ESM\Entity\Visit;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SchemaConditionType
 * @package App\ESM\Form
 */
class SchemaConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('label', null, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'label',
                ],
            ])

            ->add('phaseVisit', ChoiceType::class, [
                'label' => 'Choix',
                'choices' => [
                    'Visite' => 'visit',
                    'Phase' => 'phase',
                ],
                'expanded' => true, // bouton radio
            ])

            ->add('condition', HiddenType::class)

            ->add('phase', EntityType::class, [
                'placeholder' => '',
                'label' => 'entity.SchemaCondition.field.phases',
                'class' => PhaseSetting::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('p')
                        ->where('p.deletedAt IS NULL')
                        ->andWhere('p.project = :project')
                        ->setParameter('project', $options['project'])
                        ->orderBy('p.label', 'ASC');
                },
                'choice_label' => 'label',
                'required' => false,
            ])

            ->add('visit', EntityType::class, [
                'placeholder' => '',
                'label' => 'entity.SchemaCondition.field.visits',
                'class' => Visit::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('v')
                        ->where('v.deletedAt IS NULL')
                        ->andWhere('v.project = :project')
                        ->setParameter('project', $options['project'])
                        ->orderBy('v.short', 'ASC');
                },
                'choice_label' => 'short',
                'required' => false,
            ])

            ->add('variable', HiddenType::class, [
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SchemaCondition::class,
        ]);

        $resolver->setRequired('project');
    }
}
