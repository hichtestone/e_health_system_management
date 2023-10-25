<?php

namespace App\ESM\Form;

use App\ESM\Entity\Center;
use App\ESM\Entity\Deviation;
use App\ESM\Entity\DropdownList\DeviationType;
use App\ESM\Entity\Institution;
use App\ESM\Entity\Patient;
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
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Class DeviationDeclarationType.
 */
class DeviationDeclarationType extends AbstractType
{
    protected $security;

    /**
     * DeviationDeclarationType constructor.
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @var bool
     */
    private $isDisabled;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->isDisabled = $options['editMode'] ? false : true;
        $roleEditing 	  = $this->security->isGranted('DEVIATION_DRAFT_EDIT', $options['data']);

        $builder
            ->add('observedAt', DateTimeType::class, [
                'label' => 'menu.project.protocol_deviation.identification.observedAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' 	=> false,
                'widget' 	=> 'single_text',
                'required'  => true,
                'format' 	=> 'dd/MM/yyyy',
                'disabled'  => !$roleEditing,
            ])
            ->add('occurenceAt', DateTimeType::class, [
                'label' => 'menu.project.protocol_deviation.identification.occurenceAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' 	=> false,
                'widget' 	=> 'single_text',
                'required'  => true,
                'format' 	=> 'dd/MM/yyyy',
                'disabled'  => !$roleEditing,
            ])
            ->add('declaredBy', EntityType::class, [
                'label' => 'menu.project.protocol_deviation.identification.declaredBy',
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('user')
                        ->innerJoin('user.userProjects', 'userProjects')
                        ->innerJoin('user.profile', 'profile')
                        ->innerJoin('profile.roles', 'roles')
                        ->innerJoin('userProjects.project', 'project')
                        ->where('project.id = :project')
                        ->andWhere('roles.code = :role')
						->andWhere('userProjects.disabledAt IS NULL')
						->orWhere('user.id = :currentUser')
                        ->setParameter('project', $options['projectID'])
                        ->setParameter('role', Role::ROLE_DEVIATION_WRITE)
                        ->setParameter('currentUser', $options['data']->getDeclaredBy()->getId())
                        ->orderBy('user.lastName', 'ASC');
                },
                'choice_label' => 'getFullName',
                'required' => true,
                'disabled' => !$roleEditing,
            ])
            ->add('type', EntityType::class, [
                'placeholder'	=> 'menu.project.protocol_deviation.description.deviation_type',
                'label' 		=> 'menu.project.protocol_deviation.description.deviation_type',
                'class' 		=> DeviationType::class,
                'required' 		=> true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('deviationType')->where('deviationType.parent IS NULL');
                },
                'disabled' => !$roleEditing,
            ])
            ->add('typeCode', EntityType::class, [
				'label' 		=> 'menu.project.protocol_deviation.description.deviation_type_code',
				'class' 		=> DeviationType::class,
				'required' 		=> true,
				'mapped' 		=> false,
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder('deviationType');
				},
                'choice_label' => function ($deviationType) {
                    return $deviationType->getCode();
                },
                'disabled' => !$roleEditing,
            ])
            ->add('center', EntityType::class, [
                'placeholder' 	=> 'menu.project.protocol_deviation.description.center',
                'label' 		=> 'menu.project.protocol_deviation.description.center',
                'class' 		=> Center::class,
                'required' 		=> false,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('center')
                        ->leftJoin('center.project', 'project')
                        ->where('project.id =:projectID')
                        ->setParameter('projectID', $options['projectID']);
                },
                'disabled' => !$roleEditing,
            ])
            ->add('institution', ChoiceType::class, [
                'placeholder' 	=> 'menu.project.protocol_deviation.description.institution',
                'label' 		=> 'menu.project.protocol_deviation.description.institution',
                'required' 		=> false,
                'disabled' 		=> !$roleEditing,
            ])
            ->add('patient', ChoiceType::class, [
                'placeholder' 	=> 'menu.project.protocol_deviation.description.patient',
                'label' 		=> 'menu.project.protocol_deviation.description.patient',
                'required' 		=> false,
                'disabled' 		=> !$roleEditing,
            ])
            ->add('resume', TextType::class, [
                'label' 		=> 'menu.project.protocol_deviation.description.resume',
				'constraints' => new Length([
					'max' => 40,
					'maxMessage' => 'Le champ résumé doit avoir au maximum 40 caractères.'
				]),
                'required' 		=> true,
                'disabled' 		=> !$roleEditing,
            ])
            ->add('description', TextareaType::class, [
                'label' 	=> 'menu.project.protocol_deviation.description.reason',
                'required' 	=> true,
                'disabled' 	=> !$roleEditing,
            ])
            ->add('potentialImpact', ChoiceType::class, [
                'placeholder' => 'menu.project.protocol_deviation.description.potentialImpact',
                'label'		  => 'menu.project.protocol_deviation.description.potentialImpact',
                'required' 	  => false,
                'choices' 	  => array_flip(Deviation::POTENTIAL_IMPACT),
                'disabled' 	  => !$roleEditing,
            ])
            ->add('potentialImpactDescription', TextType::class, [
                'label' 	=> 'menu.project.protocol_deviation.description.potentialImpactDescription',
                'required' 	=> false,
                'disabled' 	=> !$roleEditing,
            ])
            ->add('causality', ChoiceType::class, [
                'label' 	=> 'menu.project.protocol_deviation.cause.causality',
                'required' 	=> true,
                'choices' 	=> array_flip(Deviation::CAUSALITY),
                'expanded' 	=> true,
                'multiple' 	=> true,
                'choice_attr' => function ($val, $key, $index) {
                    return ['disabled' => 'disabled'];
                },
                'block_prefix' => 'wrapped_causality',
                'disabled' => !$roleEditing,
            ])
            ->add('causalityDescription', TextareaType::class, [
                'label' => 'menu.project.protocol_deviation.cause.causality_reason',
                'required' => true,
                'disabled' => !$roleEditing,
            ])
            ->add('grade', ChoiceType::class, [
                'placeholder' => 'menu.project.protocol_deviation.cause.grade',
                'label' => 'Grade',
                'required' => true,
                'choices' => array_flip(Deviation::GRADES),
                'disabled' => !$roleEditing,
            ])
            ->add('efficiencyMeasure', ChoiceType::class, [
                'label' => 'menu.project.protocol_deviation.action.efficiencyMeasure',
                'choices' => array_flip(Deviation::EFFICIENCY_MEASURE),
                'required' => true,
            ])
            ->add('efficiencyJustify', TextareaType::class, [
                'label' => 'menu.project.protocol_deviation.action.efficiencyJustify',
                'required' => false,
                'disabled' => !$roleEditing,
            ])
            ->add('notEfficiencyMeasureReason', TextareaType::class, [
                'label' => 'menu.project.protocol_deviation.action.notEfficiencyMeasureReason',
                'required' => false,
                'disabled' => !$roleEditing,
                'empty_data' => null,
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);
        $builder->addEventListener(FormEvents::PRE_SUBMIT,   [$this, 'onPreSubmit']);
        $builder->addEventListener(FormEvents::POST_SUBMIT,  [$this, 'onPostSubmit']);
    }

    public function onPreSetData(FormEvent $event): void
    {
        $roleEditing = $this->security->isGranted('DEVIATION_DRAFT_EDIT', $event->getData());

        $deviation = $event->getData();
        $form = $event->getForm();
        $type = $deviation->getType();
        $subType = $deviation->getSubType();
        $center = $deviation->getCenter();

        if ($type) {
            $form->add('subType', EntityType::class, [
                'placeholder' => 'menu.project.protocol_deviation.description.deviation_sub_type',
                'label' => 'menu.project.protocol_deviation.description.deviation_sub_type',
                'class' => DeviationType::class,
                'required' => true,
                'query_builder' => function (EntityRepository $er) use ($deviation) {
                    return $er->createQueryBuilder('deviationType')
                        ->where('deviationType.parent =:parentID')
                        ->setParameter('parentID', $deviation->getType());
                },
                'disabled' => !$roleEditing,
            ]);
        } else {
            $form->add('subType', EntityType::class, [
                'placeholder' => 'menu.project.protocol_deviation.description.deviation_sub_type',
                'label' => 'menu.project.protocol_deviation.description.deviation_sub_type',
                'class' => DeviationType::class,
                'required' => true,
                'choices' => [],
                'disabled' => !$roleEditing,
            ]);
        }

        if ($subType) {
            $subType = $deviation->getSubType();

            $form->add('subTypeCode', EntityType::class, [
                'label' => 'menu.project.protocol_deviation.description.deviation_sub_type_code',
                'class' => DeviationType::class,
                'required' => false,
                'mapped' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('deviationType');
                },
                'choice_label' => function ($deviationType) {
                    return $deviationType->getCode();
                },
                'data' => $subType,
                'disabled' => !$roleEditing,
            ]);
        } else {
            $form->add('subTypeCode', EntityType::class, [
                'label' => 'menu.project.protocol_deviation.description.deviation_sub_type_code',
                'class' => DeviationType::class,
                'required' => false,
                'mapped' => false,
                'choice_label' => function ($deviationType) {
                    return $deviationType->getCode();
                },
                'disabled' => !$roleEditing,
            ]);
        }

        if ($center) {
            $form->add('institution', EntityType::class, [
                'placeholder' => 'menu.project.protocol_deviation.description.institution',
                'label' => 'menu.project.protocol_deviation.description.institution',
                'class' => Institution::class,
                'required' => false,
                'query_builder' => function (EntityRepository $er) use ($center) {
                    return $er->createQueryBuilder('institution')
                        ->leftJoin('institution.centers', 'centers')
                        ->andWhere('centers.id =:centerID')
                        ->setParameter('centerID', $center->getId());
                },
                'choice_label' => function ($institution) {
                    return $institution->getName();
                },
                'data' => $deviation->getInstitution(),
                'empty_data' => null,
                'disabled' => !$roleEditing,
            ]);

            $form->add('patient', EntityType::class, [
                'placeholder' => 'menu.project.protocol_deviation.description.patient',
                'label' => 'menu.project.protocol_deviation.description.patient',
                'class' => Patient::class,
                'required' => false,
                'query_builder' => function (EntityRepository $er) use ($center) {
                    return $er->createQueryBuilder('patient')
                        ->leftJoin('patient.center', 'center')
                        ->andWhere('center.id =:centerID')
                        ->setParameter('centerID', $center->getId());
                },
                'choice_label' => function ($patient) {
                    return $patient->getNumber();
                },
                'data' => $deviation->getPatient(),
                'empty_data' => null,
                'disabled' => !$roleEditing,
            ]);
        } else {
            $form->add('institution', EntityType::class, [
                'placeholder' => 'menu.project.protocol_deviation.description.institution',
                'label' => 'menu.project.protocol_deviation.description.institution',
                'class' => Institution::class,
                'required' => false,
                'choices' => [],
                'disabled' => !$roleEditing,
            ]);

            $form->add('patient', EntityType::class, [
                'placeholder' => 'menu.project.protocol_deviation.description.patient',
                'label' => 'Patient',
                'class' => Patient::class,
                'required' => false,
                'choices' => [],
                'disabled' => !$roleEditing,
            ]);
        }
    }

    public function onPreSubmit(FormEvent $event): void
    {
        $deviation = $event->getData();
        $form = $event->getForm();

        if (isset($deviation['type'])) {
            $form->remove('subType');
            $form->add('subType', EntityType::class, [
                'placeholder' => 'menu.project.protocol_deviation.description.deviation_sub_type',
                'label' => 'menu.project.protocol_deviation.description.deviation_sub_type',
                'class' => DeviationType::class,
                'required' => true,
                'query_builder' => function (EntityRepository $er) use ($deviation) {
                    return $er->createQueryBuilder('deviationType')
                        ->where('deviationType.parent =:parentID')
                        ->setParameter('parentID', $deviation['type']);
                },
            ]);
        } else {
            $form->add('subType', EntityType::class, [
                'placeholder' => 'menu.project.protocol_deviation.description.deviation_sub_type',
                'label' => 'menu.project.protocol_deviation.description.deviation_sub_type',
                'class' => DeviationType::class,
                'required' => false,
            ]);
        }

        if (isset($deviation['center'])) {
            $form->remove('institution');
            $form->add('institution', EntityType::class, [
                'placeholder' => 'menu.project.protocol_deviation.description.institution',
                'label' => 'menu.project.protocol_deviation.description.institution',
                'class' => Institution::class,
                'required' => false,
                'query_builder' => function (EntityRepository $er) use ($deviation) {
                    return $er->createQueryBuilder('institution')
                        ->leftJoin('institution.centers', 'centers')
                        ->andWhere('centers.id =:centerID')
                        ->setParameter('centerID', $deviation['center']);
                },
                'choice_label' => function ($institution) {
                    return $institution->getName();
                },
                'empty_data' => null,
            ]);

            $form->remove('patient');
            $form->add('patient', EntityType::class, [
                'placeholder' => 'menu.project.protocol_deviation.description.patient',
                'label' => 'menu.project.protocol_deviation.description.patient',
                'class' => Patient::class,
                'required' => false,
                'query_builder' => function (EntityRepository $er) use ($deviation) {
                    return $er->createQueryBuilder('patient')
                        ->leftJoin('patient.center', 'center')
                        ->andWhere('center.id =:centerID')
                        ->setParameter('centerID', $deviation['center']);
                },
                'choice_label' => function ($patient) {
                    return $patient->getNumber();
                },
                'empty_data' => null,
            ]);
        }
    }

    public function onPostSubmit(FormEvent $event): void
    {
        $form 						= $event->getForm();
        $causality 					= $form->get('causality')->getData();
        $type 						= $form->get('type')->getData();
        $subType 					= $form->get('subType')->getData();
        $potentialImpact 			= $form->get('potentialImpact')->getData();
        $potentialImpactDescription = $form->get('potentialImpactDescription')->getData();

        if (empty($causality)) {
            $form->get('causality')->addError(new FormError('Obligatoire'));
        }

        if (isset($type)) {
            if (!$subType) {
                $form->get('subType')->addError(new FormError('Obligatoire'));
            }
        }

        if ($potentialImpact === 99 && !$potentialImpactDescription) {
            $form->get('potentialImpactDescription')->addError(new FormError('Obligatoire'));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Deviation::class,
            'projectID' => null,
            'editMode' => null,
        ]);
    }
}
