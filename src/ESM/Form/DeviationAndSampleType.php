<?php

namespace App\ESM\Form;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationAndSample;
use App\ESM\Entity\DeviationSample;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DeviationAndSampleType
 * @package App\ESM\Form
 */
class DeviationAndSampleType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		if ($options['isSample']) {
			$builder
				->add('deviationSample', EntityType::class, [
					'class' => DeviationSample::class,
					'query_builder' => function (EntityRepository $er) use ($options) {
						return $er->createQueryBuilder('deviationSample')
							->where('deviationSample.id = :deviationSampleID')
							->setParameter('deviationSampleID', $options['deviationSampleID']);
					},
					'choice_label' => 'code',
					'required' => true,
					'attr' => [
						'hidden' => true,
					],
					'label' => false,
				]);
			$builder
				->add('deviation', EntityType::class, [
					'label' => 'Code de la déviation :',
					'class' => Deviation::class,
					'query_builder' => function (EntityRepository $er) use ($options) {
						if (empty($options['deviationIDs'])) {
							return $er->createQueryBuilder('deviation')
								->where('deviation.status in (:deviations)')
								->andWhere('deviation.project in (:projects)')
								->setParameter('deviations', [Deviation::STATUS_IN_PROGRESS, Deviation::STATUS_DONE])
								->setParameter('projects', $options['projects']);
						} else {
							return $er->createQueryBuilder('deviation')
								->where('deviation.id not in (:deviationIDs)')
								->andWhere('deviation.status in (:deviations)')
								->andWhere('deviation.project in (:projects)')
								->setParameter('deviations', [Deviation::STATUS_IN_PROGRESS, Deviation::STATUS_DONE])
								->setParameter('projects', $options['projects'])
								->setParameter('deviationIDs', $options['deviationIDs']);
						}

					},
					'choice_label' => 'code',
					'required' => false,
				]);
			$builder
				->get('deviation')->addEventListener(
					FormEvents::POST_SUBMIT,
					function (FormEvent $event) {
						$form = $event->getForm();
						$data = $form->getData();
						$this->addDescriptionField($form->getParent(), $data);
					}
				);
			$builder->addEventListener(
				FormEvents::POST_SET_DATA,
				function (FormEvent $event) {
					$data = $event->getData();
					$deviation = $data->getDeviation();
					$form = $event->getForm();
					$this->addDescriptionField($form, $deviation);
				}
			);
		} else {
			$builder
				->add('deviation', EntityType::class, [
					'class' => Deviation::class,
					'query_builder' => function (EntityRepository $er) use ($options) {
						return $er->createQueryBuilder('deviation')
							->where('deviation.id = :deviationID')
							->setParameter('deviationID', $options['deviationID']);
					},
					'choice_label' => 'code',
					'required' => true,
					'attr' => [
						'hidden' => true,
					],
					'label' => false,
				]);
			$builder
				->add('deviationSample', EntityType::class, [
					'label' => 'Code de la déviation échantillon biologique:',
					'class' => DeviationSample::class,
					'query_builder' => function (EntityRepository $er) use ($options) {
						if (empty($options['deviationSampleIDs'])) {
							return $er->createQueryBuilder('deviationSample')
								->innerJoin('deviationSample.projects', 'project')
								->where('deviationSample.status in (:deviationSamples)')
								->andWhere('project.id in (:project)')
								->setParameter('deviationSamples', [Deviation::STATUS_IN_PROGRESS, Deviation::STATUS_DONE])
								->setParameter('project', $options['project']);
						} else {
							return $er->createQueryBuilder('deviationSample')
								->innerJoin('deviationSample.projects', 'project')
								->where('deviationSample.id not in (:deviationSampleIDs)')
								->andWhere('deviationSample.status in (:deviationSamples)')
								->andWhere('project.id in (:project)')
								->setParameter('deviationSamples', [Deviation::STATUS_IN_PROGRESS, Deviation::STATUS_DONE])
								->setParameter('project', $options['project'])
								->setParameter('deviationSampleIDs', $options['deviationSampleIDs']);
						}

					},
					'choice_label' => 'code',
					'required' => false,
				]);
			$builder
				->get('deviationSample')->addEventListener(
					FormEvents::POST_SUBMIT,
					function (FormEvent $event) {
						$form = $event->getForm();
						$data = $form->getData();
						$this->addDescriptionSampleField($form->getParent(), $data);
					}
				);
			$builder->addEventListener(
				FormEvents::POST_SET_DATA,
				function (FormEvent $event) {
					$data = $event->getData();
					$deviationSample = $data->getDeviationSample();
					$form = $event->getForm();
					$this->addDescriptionSampleField($form, $deviationSample);
				}
			);
		}


	}

	/**
	 * @param FormInterface $form
	 * @param null $deviation
	 */
	public function addDescriptionField(FormInterface $form, $deviation = null)
	{
		$builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
			'resume',
			TextareaType::class,
			null,
			[
				'label' => 'Résumé de la déviation :',
				'required' => false,
				'auto_initialize' => false,
				'data' => null != $deviation ? $deviation->getResume() : '',
				'mapped' => false,
				'disabled' => true,
			]
		);

		$form->add($builder->getForm());

	}
	/**
	 * @param FormInterface $form
	 * @param null $deviationSample
	 */
	public function addDescriptionSampleField(FormInterface $form, $deviationSample = null)
	{
		$builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
			'resume',
			TextareaType::class,
			null,
			[
				'label' => 'Résumé de la déviation échantillon biologique: ',
				'required' => false,
				'auto_initialize' => false,
				'data' => null != $deviationSample ? $deviationSample->getResume() : '',
				'mapped' => false,
				'disabled' => true,
			]
		);

		$form->add($builder->getForm());

	}


	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver
			->setDefaults([
				'data_class' => DeviationAndSample::class,
			])
			->setRequired(['deviationSampleID', 'projects', 'deviationIDs', 'associate', 'isSample', 'deviationID', 'project', 'deviationSampleIDs'])
		;
	}
}
