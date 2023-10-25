<?php

namespace App\ESM\Form;

use App\ESM\Entity\DeviationAction;
use App\ESM\Entity\DeviationSystemAction;
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
 * Class DeviationSystemActionType
 * @package App\ESM\Form
 */
class DeviationSystemActionType extends AbstractType
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

				->add('intervener', EntityType::class, [
					'class' => User::class,
					'choice_label' => 'getFullName',
					'required' => true,
					'label' => 'entity.Deviation.DeviationReviewAction.field.intervenant',
					'query_builder' => function (EntityRepository $er) use ($options) {
						return $er->createQueryBuilder('u')
							->orderBy('u.lastName', 'ASC');
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

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver
			->setDefaults(['data_class' => DeviationSystemAction::class])
			->setRequired(['deleteAction'])
		;
	}
}
