<?php

namespace App\ESM\Form;

use App\ESM\Entity\DeviationReviewAction;
use App\ESM\Entity\DeviationSystemReviewAction;
use App\ESM\Entity\Interlocutor;
use App\ESM\Entity\Role;
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
 * Class DeviationSystemReviewActionType
 * @package App\ESM\Form
 */
class DeviationSystemReviewActionType extends AbstractType
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
                ->add('intervener', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => 'getFullName',
                    'required' => false,
                    'label' => 'entity.Deviation.DeviationReviewAction.field.intervenant',
					'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('user')
							->leftJoin('user.profile', 'profile')
							->leftJoin('profile.roles', 'roles')
							->andWhere('roles.code IN (:roles)')
							->setParameters(['roles' => Role::ROLE_NO_CONFORMITY_SYSTEM_QA_WRITE])
							->orderBy('user.lastName', 'ASC');
					},
                ])
            ;
        }

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
            'data_class' => DeviationSystemReviewAction::class,
            ])
            ->setRequired(['deleteReviewAction'])
        ;
    }
}
