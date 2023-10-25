<?php

namespace App\ESM\Form;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationSample;
use App\ESM\Entity\DropdownList\DeviationSample\DecisionTaken;
use App\ESM\Entity\DropdownList\DeviationSample\DetectionCenter;
use App\ESM\Entity\DropdownList\DeviationSample\DetectionContext;
use App\ESM\Entity\DropdownList\DeviationSample\NatureType;
use App\ESM\Entity\DropdownList\DeviationSample\PotentialImpactSample;
use App\ESM\Entity\DropdownList\DeviationSample\ProcessInvolved;
use App\ESM\Entity\Institution;
use App\ESM\Entity\Project;
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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichFileType;

/**
 * Class DeviationSampleDeclarationType
 * @package App\ESM\Form
 */
class DeviationSampleDeclarationType extends AbstractType
{
	/**
	 * @var Security $security
	 */
	protected $security;

	public function __construct(Security $security)
	{
		$this->security = $security;
	}

	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$canActivePlanAction = false;

		if (isset($options['data'])) {
			$canActivePlanAction = !(($options['data']->getGrade() == Deviation::GRADE_MAJEUR
				|| $options['data']->getGrade() == Deviation::GRADE_CRITIQUE)
				&& Deviation::STATUS_DONE != $options['data']->getStatus());
		}

        $builder
			->add('observedAt',DateTimeType::class, [
				'label' => 'entity.NoConformity.field.sample.declaration.observedAt',
				'attr' => [
					'placeholder' => 'dd/MM/yyyy',
					'class' => 'js-datepicker',
				],
				'html5' => false,
				'widget' => 'single_text',
				'required' => true,
				'format' => 'dd/MM/yyyy',
			])
            ->add('occurrenceAt',DateTimeType::class, [
				'label' => 'entity.NoConformity.field.sample.declaration.occurrenceAt',
				'attr' => [
					'placeholder' => 'dd/MM/yyyy',
					'class' => 'js-datepicker',
				],
				'html5' 	=> false,
				'widget' 	=> 'single_text',
				'required'  => true,
				'format' 	=> 'dd/MM/yyyy',
			])
			->add('declaredBy',EntityType::class, [
				'label' => 'entity.NoConformity.field.sample.declaration.declaredBy',
				'class' => User::class,
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder('user')
						->innerJoin('user.profile', 'profile')
						->innerJoin('profile.roles', 'roles')
						->andWhere('roles.code = :role')
						->setParameter('role', Role::ROLE_DEVIATION_WRITE)
						->orderBy('user.lastName', 'ASC');
				},
				'choice_label' => 'getFullName',
				'required' => true,
			]);
		if ($options['editMode'] === 'edit') {
			$builder->add('projects', EntityType::class, [
				'class' => Project::class,
				'label' => 'entity.NoConformity.field.sample.declaration.projects',
				'query_builder' => function (EntityRepository $er) use ($options) {
					return $er->createQueryBuilder('project')
						->where('project.closedAt IS NULL')
						->orderBy('project.acronyme', 'ASC');
				},
				'choice_label' => 'acronyme',
				'multiple' => true,
				'expanded' => false,
				'required' => true,
			]);
			$builder
				->get('projects')->addEventListener(
					FormEvents::POST_SUBMIT,
					function (FormEvent $event) {
						$form = $event->getForm();
						$data = $form->getData()->toArray();
						$this->addInstitutionField($form->getParent(), $data);
					}
				);
			$builder->addEventListener(
				FormEvents::POST_SET_DATA,
				function (FormEvent $event) {
					$data = $event->getData();
					$projects = $data->getProjects();
					$form = $event->getForm();
					// On crée le champ en le laissant vide (champs utilisé pour le JavaScript)
					$this->addInstitutionField($form, $projects);
				}
			);
		}

		$builder->add('detectionContext',EntityType::class, [
				'label' => 'entity.NoConformity.field.sample.declaration.detectionContext',
				'class' => DetectionContext::class,
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder('detectionContext');
				},
				'choice_label' => 'label',
				'required' => true,
			])
			->add('detectionContextReason', TextType::class, [
				'label' => 'entity.NoConformity.field.sample.declaration.detectionContextReason',
				'required' => false,
			])
			->add('detectionCenter',EntityType::class, [
				'label' => 'entity.NoConformity.field.sample.declaration.detectionCenter',
				'class' => DetectionCenter::class,
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder('detectionCenter');
				},
				'choice_label' => 'label',
				'required' => true,
			])
			->add('detectionCenterReason', TextType::class, [
				'label' => 'entity.NoConformity.field.sample.declaration.detectionCenterReason',
				'required' => false,
			]);
		if ($options['editMode'] === 'edit') {
			$builder->add('processInvolves', EntityType::class, [
				'class' => ProcessInvolved::class,
				'label' => 'entity.NoConformity.field.sample.declaration.processInvolves',
				'query_builder' => function (EntityRepository $er) use ($options) {
					return $er->createQueryBuilder('processInvolves');
				},
				'choice_label' => 'label',
				'multiple' => true,
				'expanded' => false,
				'required' => true,
			]);
		}

		$builder
			->add('processInvolvedReason', TextType::class, [
				'label' => 'entity.NoConformity.field.sample.declaration.processInvolvedReason',
				'required' => false,
			])
			->add('natureType',EntityType::class, [
				'label' => 'entity.NoConformity.field.sample.declaration.natureType',
				'class' => NatureType::class,
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder('natureType');
				},
				'choice_label' => 'label',
				'required' => true,
			])
			->add('natureTypeReason', TextType::class, [
				'label' => 'entity.NoConformity.field.sample.declaration.natureTypeReason',
				'required' => false,
			])
			->add('resume', TextType::class, [
				'label' => 'entity.NoConformity.field.sample.declaration.resume',
				'required' => true,
			])
			->add('description', TextareaType::class, [
				'label' => 'entity.NoConformity.field.sample.declaration.description',
				'required' => true,
			])
			->add('causality', ChoiceType::class, [
				'label' 	=> 'entity.NoConformity.field.sample.declaration.causality',
				'required' 	=> true,
				'choices' 	=> array_flip(Deviation::CAUSALITY),
				'expanded' 	=> true,
				'multiple' 	=> true,
				'choice_attr' => function ($val, $key, $index) {
					return ['disabled' => 'disabled'];
				},
				'block_prefix' => 'wrapped_causality',
			])
			->add('causalityDescription', TextareaType::class, [
				'label' => 'entity.NoConformity.field.sample.declaration.causalityDescription',
				'required' => true,
			])
			->add('causalityReason', TextType::class, [
				'label' => 'entity.NoConformity.field.sample.declaration.causalityReason',
				'required' => false,
			])
			->add('potentialImpactSample',EntityType::class, [
				'label' => 'entity.NoConformity.field.sample.declaration.potentialImpactSample',
				'class' => PotentialImpactSample::class,
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder('potentialImpact');
				},
				'choice_label' => 'label',
				'required' => false,
			])
			->add('potentialImpactSampleReason', TextType::class, [
				'label' => 'entity.NoConformity.field.sample.declaration.potentialImpactSampleReason',
				'required' => false,
			])
			->add('grade', ChoiceType::class, [
				'label' => 'entity.NoConformity.field.sample.declaration.grade',
				'required' => true,
				'choices' => array_flip(Deviation::GRADES),
			])
			->add('decisionTaken',EntityType::class, [
				'label' => 'entity.NoConformity.field.sample.declaration.decisionTaken',
				'class' => DecisionTaken::class,
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder('decisionTaken');
				},
				'choice_label' => 'label',
				'required' => false,
			])
			->add('decisionFileVich', VichFileType::class, [
				'label' => 'entity.NoConformity.field.sample.declaration.decisionFileVich',
				'required' => false,
				'allow_delete' => true,
				'delete_label' => 'Supprimer',
				'download_link' => false,
				'asset_helper' => true,
				'constraints' => [
					new File([
						'mimeTypes' => [
							'text/plain',
							'text/x-csv',
							'application/vnd.ms-excel',
							'application/csv',
							'application/x-csv',
							'text/csv',
							'text/comma-separated-values',
							'text/x-comma-separated-values',
							'text/tab-separated-values',
							'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
						],
						'mimeTypesMessage' => 'Seuls les formats(.xls, .xlsx et .csv) sont autorisés',
					]),
				],
			])
			->add('efficiencyMeasure', ChoiceType::class, [
				'label' => 'entity.NoConformity.field.sample.declaration.efficiencyMeasure',
				'required' => true,
				'choices' => array_flip(Deviation::EFFICIENCY_MEASURE),
				'disabled'  => $canActivePlanAction,
			])
			->add('efficiencyJustify', TextareaType::class, [
				'label' => 'entity.NoConformity.field.sample.declaration.efficiencyJustify',
				'required' => false,
				'disabled'  => $canActivePlanAction,
			])
			->add('notEfficiencyMeasureReason', TextareaType::class, [
				'label' => 'entity.NoConformity.field.sample.declaration.notEfficiencyMeasureReason',
				'required' => false,
				'disabled'  => $canActivePlanAction,
			])
        ;

		$builder->addEventListener(FormEvents::POST_SUBMIT,  [$this, 'onPostSubmit']);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
			->setDefaults([
				'data_class' => DeviationSample::class,
        	])
			->setRequired(['user', 'editMode'])
		;
    }

	/**
	 * @param FormInterface $form
	 * @param null $data
	 */
	public function addInstitutionField(FormInterface $form, $data = null)
	{
		$institutions = [];

		foreach ($data as $project) {
			$centers = $project->getCenters()->toArray();

			foreach ($centers as $center) {
				$centerInstitutions = $center->getInstitutions()->toArray();
				foreach ($centerInstitutions as $centerInstitution) {
					$institutions[] = $centerInstitution->getId();
				}
			}
		}

		$builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
			'institutions',
			EntityType::class,
			null,
			[
				'class' => Institution::class,
				'label' => 'entity.NoConformity.field.sample.declaration.institutions',
				'query_builder' => function (EntityRepository $er) use ($institutions) {
						return $er->createQueryBuilder('institution')
							->where('institution.deletedAt IS NULL')
							->andWhere('institution.id IN (:institutions)')
							->setParameter('institutions', $institutions)
							->orderBy('institution.name', 'ASC');
				},
				'choice_label' => 'name',
				'multiple' => true,
				'expanded' => false,
				'required' => false,
				'auto_initialize' => false,
			]
		);

		$form->add($builder->getForm());
	}

	public function onPostSubmit(FormEvent $event): void
	{
		$form      = $event->getForm();
		$causality = $form->get('causality')->getData();

		if (empty($causality)) {
			$form->get('causality')->addError(new FormError('Obligatoire'));
		}
	}
}
