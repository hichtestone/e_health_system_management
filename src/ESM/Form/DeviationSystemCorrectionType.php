<?php

namespace App\ESM\Form;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationCorrection;
use App\ESM\Entity\DeviationSystemCorrection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DeviationSystemCorrectionType
 * @package App\ESM\Form
 */
class DeviationSystemCorrectionType extends AbstractType
{
	private $deviationSystem;

	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		if (!$options['deleteCorrection']) {

			$deviationSystem = $options['deviationSystem'];
			$this->deviationSystem = $deviationSystem;

			$builder

				->add('description', TextType::class, [
					'label' => 'entity.DeviationSample.DeviationCorrection.field.description',
					'required' => true,
				])

				->add('realizedAt', DateTimeType::class, [
					'label' => 'entity.DeviationSample.DeviationCorrection.field.realizedAt',
					'attr' => [
						'placeholder' => 'dd/MM/yyyy',
						'class' => 'js-datepicker',
					],
					'html5' => false,
					'widget' => 'single_text',
					'required' => true,
					'format' => 'dd/MM/yyyy',
				])

				->add('applicationPlannedAt', DateTimeType::class, [
					'label' => 'entity.DeviationSample.DeviationCorrection.field.applicationPlannedAt',
					'attr' => [
						'placeholder' => 'dd/MM/yyyy',
						'class' => 'js-datepicker',
					],
					'html5' => false,
					'widget' => 'single_text',
					'required' => false,
					'format' => 'dd/MM/yyyy',
				])

				->add('efficiencyMeasure', ChoiceType::class, [
					'label' => 'entity.DeviationSample.DeviationCorrection.field.efficiencyMeasure',
					'choices' => array_flip(Deviation::EFFICIENCY_MEASURE),
					'required' => true,
				])

				->add('notEfficiencyMeasureReason', TextType::class, [
					'label' => 'entity.DeviationSample.DeviationCorrection.field.notEfficiencyMeasureReason',
					'required' => false,
					'empty_data' => null,
				]);

		} else {

			$builder->add('comment', TextareaType::class, [
				'label' => 'entity.DeviationSample.DeviationCorrection.field.comment',
				'attr' => [
					'placeholder' => 'entity.DeviationSample.DeviationCorrection.field.comment',
				],
				'required' => true,
				'data' => '',
			]);
		}
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver
			->setDefaults([
				'data_class' 	  => DeviationSystemCorrection::class,
				'deviationSystem' => null,
			])
			->setRequired(['deleteCorrection']);
	}
}
