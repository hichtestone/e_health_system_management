<?php

namespace App\ESM\Form;

use App\ESM\Entity\DeviationReviewAction;
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
 * Class DeviationReviewActionType
 * @package App\ESM\Form
 */
class DeviationReviewActionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['deleteReviewAction']) {
            $builder
                ->add('typeAction', ChoiceType::class, [
                    'label' => 'entity.Deviation.DeviationReviewAction.field.type',
                    'choices' => [
                        DeviationReviewAction::TYPE_ACTION[DeviationReviewAction::TYPE_ACTION_CORRECTIVE] => DeviationReviewAction::TYPE_ACTION_CORRECTIVE,
                        DeviationReviewAction::TYPE_ACTION[DeviationReviewAction::TYPE_ACTION_PREVENTIVE] => DeviationReviewAction::TYPE_ACTION_PREVENTIVE,
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
                        DeviationReviewAction::STATUS[DeviationReviewAction::STATUS_PROVIDE] => DeviationReviewAction::STATUS_PROVIDE,
                        DeviationReviewAction::STATUS[DeviationReviewAction::STATUS_EDITION] => DeviationReviewAction::STATUS_EDITION,
                        DeviationReviewAction::STATUS[DeviationReviewAction::STATUS_FINISH] => DeviationReviewAction::STATUS_FINISH,
                    ],
                    'required' => true,
                ])
                ->add('typeManager', ChoiceType::class, [
                    'label' => 'entity.Deviation.DeviationReviewAction.field.typeManager',
                    'choices' => [
                        DeviationReviewAction::TYPE_MANAGER[DeviationReviewAction::TYPE_MANAGER_PROJECT] => DeviationReviewAction::TYPE_MANAGER_PROJECT,
                        DeviationReviewAction::TYPE_MANAGER[DeviationReviewAction::TYPE_MANAGER_CENTER] => DeviationReviewAction::TYPE_MANAGER_CENTER,
                    ],
                    'expanded' => true,
                    'required' => true,
                    'placeholder' => false,
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
                ->add('user', EntityType::class, [
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
								->setParameter('currentUser', $options['user'])
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
            ;
        }

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
            'data_class' => DeviationReviewAction::class,
            ])
            ->setRequired(['project', 'deleteReviewAction', 'user', 'interlocutor'])
        ;
    }
}
