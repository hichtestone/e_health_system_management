<?php

namespace App\ESM\Form;

use App\ESM\Entity\PatientVariable;
use App\ESM\Entity\PhaseSetting;
use App\ESM\Entity\Visit;
use App\ESM\Validator\DelayReferenceDate;
use App\ESM\Validator\UnitVariableVisit;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class VisitSettingType
 * @package App\ESM\Form
 */
class VisitSettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('ordre', NumberType::class, [
                'label' => 'entity.VisitSetting.field.order',
                'attr' => [
                    'placeholder' => 'entity.VisitSetting.field.order',
                    'pattern' => '\d+',
                ],
                'required' => true,
            ])

			->add('phase', EntityType::class, [
                'label' => 'entity.VisitSetting.field.phase',
                'class' => PhaseSetting::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('phase')
                        ->where('phase.deletedAt IS NULL')
                        ->andWhere('phase.project = :project')
                        ->setParameter('project', $options['project'])
                        ->orderBy('phase.label', 'ASC');
                },
                'choice_label' => 'label',
                'required' => true,
            ])

            ->add('short', TextType::class, [
                'label' => 'entity.VisitSetting.field.short',
                'attr' => [
                    'placeholder' => 'entity.VisitSetting.field.short',
                ],
                'required' => true,
                'constraints' => [
                    new UnitVariableVisit(),
                ],
            ])

            ->add('label', TextType::class, [
                'label' => 'entity.VisitSetting.field.label',
                'attr' => [
                    'placeholder' => 'entity.VisitSetting.field.label',
                ],
                'required' => false,
            ])

            ->add('price', NumberType::class, [
                'label' => 'entity.VisitSetting.field.price',
                'attr' => [
                    'placeholder' => 'entity.VisitSetting.field.price',
                ],
                'required' => false,
            ])

            ->add('delay', NumberType::class, [
                'label' => 'entity.VisitSetting.field.delay',
                'attr' => [
                    'placeholder' => 'entity.VisitSetting.field.delay',
					'pattern' => '\d+',
                ],
                'required' => false,
            ])

            ->add('delayApprox', NumberType::class, [
                'label' => 'entity.VisitSetting.field.delayApprox',
                'attr' => [
                    'placeholder' => 'entity.VisitSetting.field.delayApprox',
					'pattern' => '\d+',
                ],
                'required' => false,
            ])

            ->add('project', TextType::class, [
                'mapped' => false,
				'data' => $options['project']->getId(),
                'attr' => [
                    'hidden' => true,
                ],
                'label' => false,
            ]);

        if ('' !== $options['variable']) {
            $builder->add('patientVariable', EntityType::class, [
                    'label' => 'entity.VisitSetting.field.patientVariable',
                    'class' => PatientVariable::class,
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('p')
                            ->where('p.sys = true OR p.isVisit = true')
                            ->andWhere('p.deletedAt IS NULL')
                            ->andWhere('p.variableType = 2') // 2 --> champ date Todo
                            ->andWhere('p.project = :project')
                            ->andWhere('p.id != :idVariable')
                            ->setParameter('project', $options['project'])
                            ->setParameter('idVariable', $options['variable'])
                            ->orderBy('p.label', 'ASC');
                    },
                    'choice_label' => 'label',
                    'required' => false,
                    'constraints' => [
                        new DelayReferenceDate(),
                    ],
                ]);
        } else {
            $builder->add('patientVariable', EntityType::class, [
                    'label' => 'entity.VisitSetting.field.patientVariable',
                    'class' => PatientVariable::class,
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('p')
                            ->where('p.sys = true OR p.isVisit = true')
                            ->andWhere('p.deletedAt IS NULL')
                            ->andWhere('p.variableType = 2') // 2 --> champ date Todo
                            ->andWhere('p.project = :project')
                            ->setParameter('project', $options['project'])
                            ->orderBy('p.label', 'ASC');
                    },
                    'choice_label' => 'label',
                    'required' => false,
                    'constraints' => [
                        new DelayReferenceDate(),
                    ],
                ]);
        }

		$builder->add('project', HiddenType::class, [
			'mapped' => false,
			'data' => $options['data']->getProject()->getId() ?? null,
		]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Visit::class,
        ]);

        $resolver->setRequired('project');
        $resolver->setRequired('variable');
    }
}
