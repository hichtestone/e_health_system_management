<?php

namespace App\ESM\Form;

use App\ESM\Entity\DeviationReview;
use App\ESM\Entity\DeviationSystemReview;
use App\ESM\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DeviationSystemReviewType
 * @package App\ESM\Form
 */
class DeviationSystemReviewType extends AbstractType
{
	public const ROLE_DEVIATION_REVIEW = 'ROLE_DEVIATION_REVIEW';

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		if (!$options['deleteOrCloseReview']) {
			if (!$options['isCrex']) {
				$builder
					->add('reader', EntityType::class, [
						'label' => 'entity.Deviation.DeviationReview.field.readerName',
						'class' => User::class,
						'query_builder' => function (EntityRepository $er) use ($options) {
							return $er->createQueryBuilder('u')
								->innerJoin('u.profile', 'p')
								->innerJoin('p.roles', 'r')
								->andWhere('r.code = :role')
								->setParameter('role', DeviationSystemReviewType::ROLE_DEVIATION_REVIEW)
								->orderBy('u.lastName', 'ASC');
						},
						'choice_label' => 'getFullName',
						'required' => true,
					]);
				}

			$builder
				->add('comment', TextareaType::class, [
					'label' => 'entity.Deviation.DeviationReview.field.comment',
					'attr' => [
						'placeholder' => 'entity.Deviation.DeviationReview.field.comment',
					],
					'required' => false,
				])

				->add('doneAt', DateTimeType::class, [
					'label' => 'entity.Deviation.DeviationReview.field.doneAtReview',
					'attr' => [
						'placeholder' => 'dd/MM/yyyy',
						'class' => 'js-datepicker',
					],
					'html5' => false,
					'widget' => 'single_text',
					'required' => true,
					'format' => 'dd/MM/yyyy',
				])

				->add('deviationIds', HiddenType::class, [
					'mapped' => false,
				])
			;
		}
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver
			->setDefaults(['data_class' => DeviationSystemReview::class])
			->setRequired(['deleteOrCloseReview', 'isCrex'])
		;
	}
}
