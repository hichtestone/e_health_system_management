<?php

namespace App\ESM\Form;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationSystem;
use App\ESM\Entity\DropdownList\DeviationSystem\ProcessSystem;
use App\ESM\Entity\Role;
use App\ESM\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
 * Class DeviationSystemDeclarationType.
 */
class DeviationSystemDeclarationType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$modeEdit 	= (bool) $options['editMode'];
		$modeQA		= (bool) $options['editQA'];

		$builder

		// IDENTIFICATION ----------------------------------------------------------------------------

			->add('observedAt', DateTimeType::class, [
				'label' => 'entity.NoConformity.field.system.declaration.observedAt',
				'attr' => [
					'placeholder' => 'dd/MM/yyyy',
					'class' => 'js-datepicker',
				],
				'html5' 	=> false,
				'widget' 	=> 'single_text',
				'required'  => true,
				'format' 	=> 'dd/MM/yyyy',
				'disabled'  => !$modeEdit || $modeQA,
			])

			->add('declaredBy', EntityType::class, [
				'label' => 'entity.NoConformity.field.system.declaration.declaredBy',
				'class' => User::class,
				'query_builder' => function (EntityRepository $er) use ($options) {
					return $er->createQueryBuilder('user')
						->innerJoin('user.profile', 'profile')
						->innerJoin('profile.roles', 'roles')
						->andWhere('roles.code IN (:roles)')
						->setParameter('roles', ['ROLE_NO_CONFORMITY_SYSTEM_WRITE'])
						->orderBy('user.lastName', 'ASC');
				},
				'choice_label' => 'getFullName',
				'required' => true,
				'disabled' => !$modeEdit || $modeQA,
			]);

			if ($modeEdit && !$modeQA) {

				$builder->add('process', EntityType::class, [
					'class' => ProcessSystem::class,
					'label' => 'entity.NoConformity.field.system.declaration.process',
					'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('processusSystem')
							->orderBy('processusSystem.code', 'ASC');
					},
					'choice_label' => function ($process) {
						return $process->getCode() . ' - ' . $process->getLabel();
					},
					'multiple' => true,
					'expanded' => false,
					'required' => true,
				]);
			}

		// IDENTIFICATION QA ----------------------------------------------------------------------------

		$builder

			->add('activity', TextType::class, [
				'label' => 'entity.NoConformity.field.system.declaration.activity',
				'required' => false,
				'disabled' => !$modeQA,
			])

			->add('referentQA', EntityType::class, [
				'label' => 'entity.NoConformity.field.system.declaration.refQA',
				'placeholder' => 'entity.NoConformity.field.system.declaration.refQA',
				'required' => false,
				'disabled' => !$modeQA,
				'class' => User::class,
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder('user')
						->leftJoin('user.profile', 'profile')
						->leftJoin('profile.roles', 'roles')
						->andWhere('roles.code IN (:roles)')
						->setParameters(['roles' => Role::ROLE_NO_CONFORMITY_SYSTEM_WRITE])
						->orderBy('user.lastName', 'ASC');
				},
			])

			->add('refISO9001', TextType::class, [
				'label' => 'entity.NoConformity.field.system.declaration.refIso',
				'required' => false,
				'disabled' => !$modeQA,
			])

			->add('document', TextType::class, [
				'label' => 'entity.NoConformity.field.system.declaration.document',
				'required' => false,
				'disabled' => !$modeQA,
			])

		// DESCRIPTION ----------------------------------------------------------------------------

			->add('resume', TextType::class, [
				'label' 		=> 'entity.NoConformity.field.system.declaration.resume',
				'required' 		=> true,
				'disabled' 		=> !$modeEdit || $modeQA,
			])

			->add('description', TextareaType::class, [
				'label' 	=> 'entity.NoConformity.field.system.declaration.description',
				'required' 	=> true,
				'disabled' 	=> !$modeEdit || $modeQA,
			])

		// CAUSES ----------------------------------------------------------------------------

			->add('causality', ChoiceType::class, [
				'label' 	=> 'entity.NoConformity.field.system.declaration.causality',
				'required' 	=> $modeQA,
				'choices' 	=> array_flip(Deviation::CAUSALITY),
				'expanded' 	=> true,
				'multiple' 	=> true,
				'choice_attr' => function ($val, $key, $index) {
					return ['disabled' => 'disabled'];
				},
				'block_prefix' => 'wrapped_causality',
				'disabled' => !$modeQA,
			])

			->add('causalityDescription', TextareaType::class, [
				'label' => 'entity.NoConformity.field.system.declaration.causality_reason',
				'required' => false,
				'disabled' => !$modeQA,
			])

			->add('grade', ChoiceType::class, [
				'placeholder' => '<<Grade>>',
				'label' => 'entity.NoConformity.field.system.declaration.grade',
				'required' => false,
				'choices' => array_flip(Deviation::GRADES),
				'disabled' => !$modeQA,
			])

			->add('potentialImpact', ChoiceType::class, [
				'placeholder' => 'entity.NoConformity.field.system.declaration.potentiel_impact',
				'label'		  => 'entity.NoConformity.field.system.declaration.potentiel_impact',
				'required' 	  => false,
				'choices' 	  => array_flip(Deviation::POTENTIAL_IMPACT),
				'disabled' 	  => !$modeQA,
			])

			->add('potentialImpactDescription', TextType::class, [
				'label' 	=> 'entity.NoConformity.field.system.declaration.potentiel_impact_reason',
				'required' 	=> false,
				'disabled' 	=> !$modeQA,
			])

		// Quality Assurance ----------------------------------------------------------------------------

			->add('visaPilotProcessChiefQA', TextType::class, [
				'label'		=> 'entity.NoConformity.field.system.declaration.visaPilotProcessChiefQA',
				'required' 	=> false,
				'disabled' 	=> !($modeQA && $options['data']->getGrade() === Deviation::GRADE_CRITIQUE),
			])

			->add('visaAt', DateTimeType::class, [
				'label' => 'entity.NoConformity.field.system.declaration.visaAt',
				'attr' 	=> [
					'placeholder' => 'dd/MM/yyyy',
					'class' => 'js-datepicker',
				],
				'html5' 	=> false,
				'widget' 	=> 'single_text',
				'required'  => false,
				'format' 	=> 'dd/MM/yyyy',
				'disabled' 	=> !($modeQA && $options['data']->getGrade() === Deviation::GRADE_CRITIQUE),
			])

			->add('officialQA', EntityType::class, [
				'label' 		=> 'entity.NoConformity.field.system.declaration.officialQA',
				'class' 		=> User::class,
				'placeholder' 	=> 'entity.NoConformity.field.system.declaration.officialQA',
				'required' 	  	=> false,
				'disabled' 		=> !($modeQA && $options['data']->getGrade() === Deviation::GRADE_CRITIQUE),
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder('user')
						->leftJoin('user.profile', 'profile')
						->leftJoin('profile.roles', 'roles')
						->andWhere('roles.code IN (:roles)')
						->setParameters(['roles' => Role::ROLE_NO_CONFORMITY_SYSTEM_QA_WRITE])
						->orderBy('user.lastName', 'ASC');
				},
			])

		// Plan d'action -------------------------------------------------------------------------------

			->add('efficiencyMeasure', ChoiceType::class, [
				'label' 		=> 'entity.NoConformity.field.system.declaration.efficiencyMeasure',
				'placeholder' 	=> 'entity.NoConformity.field.system.declaration.efficiencyMeasure',
				'choices' 		=> array_flip(Deviation::EFFICIENCY_MEASURE),
				'required' 		=> true,
				'disabled' 		=> !$modeEdit || !$modeQA,
		])
			->add('efficiencyJustify', TextareaType::class, [
				'label' 		=> 'entity.NoConformity.field.system.declaration.efficiencyJustify',
				'required' 		=> false,
				'disabled' 		=> !$modeEdit || !$modeQA,
			])
			->add('notEfficiencyMeasureReason', TextareaType::class, [
				'label' 		=> 'entity.NoConformity.field.system.declaration.notEfficiencyMeasureReason',
				'required' 		=> false,
				'empty_data' 	=> null,
				'disabled' 		=> !$modeEdit || !$modeQA,
			])
		;

		$builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'onPostSubmit']);
	}

	public function onPostSubmit(FormEvent $event): void
	{
		$form 						= $event->getForm();
		$deviationSystem			= $event->getData();
		$causality 					= $form->get('causality')->getData();
		$potentialImpact 			= $form->get('potentialImpact')->getData();
		$potentialImpactDescription = $form->get('potentialImpactDescription')->getData();

		if (empty($causality) && $deviationSystem->getStatus() !== Deviation::STATUS_DRAFT) {
			$form->get('causality')->addError(new FormError('Obligatoire'));
		}

		if ($potentialImpact === 99 && !$potentialImpactDescription) {
			$form->get('potentialImpactDescription')->addError(new FormError('Obligatoire'));
		}
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => DeviationSystem::class,
			'declarants' => null,
			'editMode' 	 => null,
			'editQA' 	 => null,
		]);
	}
}
