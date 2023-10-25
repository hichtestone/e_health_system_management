<?php

namespace App\ESM\Form;

use App\ESM\Entity\DeviationAction;
use App\ESM\Entity\DeviationSampleAction;
use App\ESM\Entity\Interlocutor;
use App\ESM\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DeviationSampleActionType
 * @package App\ESM\Form
 */
class DeviationSampleActionType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['deleteActionSample']) {
            $builder
				->add('typeAction', ChoiceType::class, [
					'label' => 'entity.Deviation.DeviationReviewAction.field.type',
					'required' => true,
					'choices' => array_flip(DeviationAction::TYPE_ACTION),
					'data' => DeviationAction::TYPE_ACTION[DeviationAction::TYPE_ACTION_CORRECTIVE],
				])
                ->add('description', TextareaType::class, [
                    'label' => 'entity.Deviation.DeviationReviewAction.field.description',
                    'attr' => [
                        'placeholder' => 'entity.Deviation.DeviationReviewAction.field.description',
                    ],
                    'required' => true,
                ])
                ->add('applicationAt', DateTimeType::class, [
                    'label' => 'entity.Deviation.DeviationReviewAction.field.applicationAt',
                    'attr' => [
                        'placeholder' => 'dd/MM/yyyy',
                        'class' => 'js-datepicker',
                    ],
                    'html5' => false,
                    'widget' => 'single_text',
                    'required' => false,
                    'format' => 'dd/MM/yyyy',
                ])
                ->add('doneAt', DateTimeType::class, [
                    'label' => 'entity.Deviation.DeviationReviewAction.field.doneAt',
                    'attr' => [
                        'placeholder' => 'dd/MM/yyyy',
                        'class' => 'js-datepicker',
                    ],
                    'html5' => false,
                    'widget' => 'single_text',
                    'required' => false,
                    'format' => 'dd/MM/yyyy',
                ])
				->add('status', ChoiceType::class, [
					'label' => 'entity.Deviation.DeviationReviewAction.field.status',
					'required' => false,
					'choices' => array_flip(DeviationAction::STATUS),
				])
				->add('typeManager', ChoiceType::class, [
					'label' => 'entity.Deviation.DeviationReviewAction.field.typeManager',
					'required' => true,
					'choices' => array_flip(DeviationSampleAction::TYPE_MANAGER),
					'expanded' => true,
					'placeholder' => false,
				])
				->add('user', EntityType::class, [
					'class' => User::class,
					'choice_label' => 'getFullName',
					'required' => false,
					'label' => 'entity.Deviation.DeviationReviewAction.field.intervenant',
					'query_builder' => function (EntityRepository $er) use ($options) {
						if ($options['data']->getId()) {
							return $er->createQueryBuilder('user')
								->innerJoin('user.userProjects', 'userProject')
								->innerJoin('userProject.project', 'project')
								->where('project.id in (:projects)')
								->andWhere('user.id in (:intervenants)')
								->orWhere('userProject.disabledAt IS NULL')
								->setParameter('intervenants', $options['intervenants'])
								->setParameter('projects', $options['projects'])
								->orderBy('user.lastName', 'ASC');
						} else {
							return $er->createQueryBuilder('user')
								->innerJoin('user.userProjects', 'userProject')
								->innerJoin('userProject.project', 'project')
								->where('project.id in (:projects)')
								->andWhere('userProject.disabledAt IS NULL')
								->setParameter('projects', $options['projects'])
								->orderBy('user.lastName', 'ASC');
						}

					},
				])
                ->add('interlocutor', EntityType::class, [
                    'class' => Interlocutor::class,
                    'choice_label' => 'getFullName',
                    'required' => false,
                    'label' => 'entity.Deviation.DeviationReviewAction.field.interlocutor',
                    'query_builder' => function (EntityRepository $er) use ($options) {
						if ($options['data']->getId()) {
							return $er->createQueryBuilder('interlocutor')
								->innerJoin('interlocutor.interlocutorCenters', 'interlocutorCenter')
								->innerJoin('interlocutorCenter.center', 'center')
								->innerJoin('center.project', 'project')
								->where('project.id in (:projects)')
								->andWhere('center.deletedAt IS NULL')
								->andWhere('interlocutor.deletedAt IS NULL')
								->andWhere('interlocutorCenter.disabledAt IS NULL')
								->orWhere('interlocutor.id in (:interlocutors)')
								->setParameter('interlocutors', $options['interlocutors'])
								->setParameter('projects', $options['projects'])
								->orderBy('interlocutor.lastName', 'ASC');
						} else {
							return $er->createQueryBuilder('interlocutor')
								->innerJoin('interlocutor.interlocutorCenters', 'interlocutorCenter')
								->innerJoin('interlocutorCenter.center', 'center')
								->innerJoin('center.project', 'project')
								->where('project.id in (:projects)')
								->andWhere('center.deletedAt IS NULL')
								->andWhere('interlocutor.deletedAt IS NULL')
								->andWhere('interlocutorCenter.disabledAt IS NULL')
								->setParameter('projects', $options['projects'])
								->orderBy('interlocutor.lastName', 'ASC');
						}

                    },
                ])
            ;
        } else {
            $builder->add('comment', TextareaType::class, [
                'label' => 'entity.Deviation.DeviationReviewAction.field.comment',
                'attr' => [
                    'placeholder' => 'entity.Deviation.DeviationReviewAction.field.comment',
                ],
                'required' => true,
                'data' => '',
            ]);
        }

    }

	/**
	 * @param OptionsResolver $resolver
	 */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => DeviationSampleAction::class,
            ])
            ->setRequired(['deleteActionSample', 'intervenants', 'interlocutors', 'associateDeviation', 'projects'])
        ;
    }
}
