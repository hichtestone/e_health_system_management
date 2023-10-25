<?php

namespace App\ESM\Form;

use App\ESM\Entity\DeviationAction;
use App\ESM\Entity\Interlocutor;
use App\ESM\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DeviationActionType
 * @package App\ESM\Form
 */
class DeviationActionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['deleteAction']) {

            $builder

                ->add('typeAction', ChoiceType::class, [
                    'label' => 'entity.Deviation.DeviationReviewAction.field.type',
                    'choices' => [
                        DeviationAction::TYPE_ACTION[DeviationAction::TYPE_ACTION_CORRECTIVE] => DeviationAction::TYPE_ACTION_CORRECTIVE,
                        DeviationAction::TYPE_ACTION[DeviationAction::TYPE_ACTION_PREVENTIVE] => DeviationAction::TYPE_ACTION_PREVENTIVE,
                    ],
                    'required' => true,
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
                    'choices' => [
                        DeviationAction::STATUS[DeviationAction::STATUS_PROVIDE] => DeviationAction::STATUS_PROVIDE,
                        DeviationAction::STATUS[DeviationAction::STATUS_EDITION] => DeviationAction::STATUS_EDITION,
                        DeviationAction::STATUS[DeviationAction::STATUS_FINISH] => DeviationAction::STATUS_FINISH,
                    ],
                    'required' => false,
                ])

                ->add('typeManager', ChoiceType::class, [
                    'label' => 'entity.Deviation.DeviationReviewAction.field.typeManager',
                    'choices' => [
                        DeviationAction::TYPE_MANAGER[DeviationAction::TYPE_MANAGER_PROJECT] => DeviationAction::TYPE_MANAGER_PROJECT,
                        DeviationAction::TYPE_MANAGER[DeviationAction::TYPE_MANAGER_CENTER] => DeviationAction::TYPE_MANAGER_CENTER,
                    ],
                    'expanded' => true,
                    'required' => true,
                    'placeholder' => false,
                ])

                ->add('intervener', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => 'getFullName',
                    'required' => true,
                    'label' => 'entity.Deviation.DeviationReviewAction.field.intervenant',
                    'query_builder' => function (EntityRepository $er) use ($options) {
						if ($options['data']->getId()) {
							return $er->createQueryBuilder('u')
								->innerJoin('u.userProjects', 'up')
								->where('up.project = :project')
								->andWhere('u.id in (:currentUser)')
								->orWhere('up.disabledAt IS NULL')
								->setParameter('currentUser', $options['intervener'])
								->setParameter('project', $options['project'])
								->orderBy('u.lastName', 'ASC');
						} else {
							return $er->createQueryBuilder('u')
								->innerJoin('u.userProjects', 'up')
								->where('up.project = :project')
								->andWhere('up.disabledAt IS NULL')
								->setParameter('project', $options['project'])
								->orderBy('u.lastName', 'ASC');

						}
                    },
                ])

				->add('interlocutor', EntityType::class, [
					'class' => Interlocutor::class,
					'choice_label' => 'getFullName',
					'required' => true,
					'label' => 'entity.Deviation.DeviationReviewAction.field.interlocutor',
					'query_builder' => function (EntityRepository $er) use ($options) {
						if ($options['data']->getId()) {
							return $er->createQueryBuilder('interlocutor')
								->innerJoin('interlocutor.interlocutorCenters', 'interlocutorCenter')
								->innerJoin('interlocutorCenter.center', 'center')
								->innerJoin('center.project', 'project')
								->where('project.id = :idProject')
								->andWhere('center.deletedAt IS NULL')
								->andWhere('interlocutor.deletedAt IS NULL')
								->andWhere('interlocutorCenter.disabledAt IS NULL')
								->orWhere('interlocutor.id in (:interlocutor)')
								->setParameter('interlocutor', $options['interlocutor'])
								->setParameter('idProject', $options['project']->getId())
								->orderBy('interlocutor.lastName', 'ASC');
						} else {
							return $er->createQueryBuilder('interlocutor')
								->innerJoin('interlocutor.interlocutorCenters', 'interlocutorCenter')
								->innerJoin('interlocutorCenter.center', 'center')
								->innerJoin('center.project', 'project')
								->where('project.id = :idProject')
								->andWhere('center.deletedAt IS NULL')
								->andWhere('interlocutor.deletedAt IS NULL')
								->andWhere('interlocutorCenter.disabledAt IS NULL')
								->setParameter('idProject', $options['project']->getId())
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

		$builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'onPostSubmit']);
	}

	public function onPostSubmit(FormEvent $event): void
	{
		$entity 		= $event->getData();
		$form 			= $event->getForm();
		$intervener 	= $entity->getIntervener();
		$interlocutor 	= $entity->getInterlocutor();
		$typeManager	= $entity->getTypeManager();

		if (!$intervener && $typeManager === DeviationAction::TYPE_MANAGER_PROJECT) {
			$form->get('intervener')->addError(new FormError('Vous devez déclarer un ou plusieurs intervenant(s) à l\'action !'));
		}

		if (!$interlocutor && $typeManager === DeviationAction::TYPE_MANAGER_CENTER) {
			$form->get('interlocutor')->addError(new FormError('Vous devez déclarer un ou plusieurs interlocuteur(s) à l\'action !'));
		}
	}

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver
            ->setDefaults(['data_class' => DeviationAction::class])
            ->setRequired(['project', 'deleteAction', 'intervener', 'interlocutor'])
        ;
    }
}
