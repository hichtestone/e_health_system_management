<?php

namespace App\ESM\Form;

use App\ESM\Entity\DropdownList\Country;
use App\ESM\Entity\DropdownList\CrfType;
use App\ESM\Entity\DropdownList\MembershipGroup;
use App\ESM\Entity\DropdownList\PatientNumber;
use App\ESM\Entity\DropdownList\PaymentUnit;
use App\ESM\Entity\DropdownList\ProjectStatus;
use App\ESM\Entity\DropdownList\Sponsor;
use App\ESM\Entity\DropdownList\StudyPopulation;
use App\ESM\Entity\DropdownList\Territory;
use App\ESM\Entity\DropdownList\TrailPhase;
use App\ESM\Entity\DropdownList\TrailType;
use App\ESM\Entity\DropdownList\TrlIndice;
use App\ESM\Entity\Project;
use App\ESM\Entity\User;
use App\ESM\Form\BaseType\TextarrayType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Class ProjectType
 * @package App\ESM\Form
 */
class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //$country = ['International' => '1', 'France' => '0'];

        $builder
            ->add('projectStatus', EntityType::class, [
                'label' => 'entity.Project.register.labels.trail_status',
                'class' => ProjectStatus::class,
                'choice_label' => 'label',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $qb = $er->createQueryBuilder('ps')
                        ->orderBy('ps.id', 'ASC');
                    if (!$options['statusDisabled']) {
                        $qb->where('ps.isAuto = 0');
                    }

                    return $qb;
                },
                'disabled' => $options['statusDisabled'],
            ])
            ->add('metasUser', TextarrayType::class, [
                'label' => 'entity.Project.register.labels.add_intervenants',
                'required' => false,
            ])
            ->add('metasParticipant', TextarrayType::class, [
                'label' => 'entity.Project.register.labels.add_investigateurs',
                'required' => false,
            ])
            ->add('trlIndice', EntityType::class, [
                'label' => 'entity.Project.register.labels.trl_indice',
                'required' => false,
                'class' => TrlIndice::class,
                'choice_label' => 'label',
            ])
            ->add('trailPhase', EntityType::class, [
                'label' => 'entity.Project.register.labels.trail_phase',
                'class' => TrailPhase::class,
                'choice_label' => 'label',
            ])
            ->add('membershipGroup', EntityType::class, [
                'label' => 'entity.Project.register.labels.membership_group',
                'required' => false,
                'class' => MembershipGroup::class,
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder('m')
						->orderBy('m.label', 'ASC')
						->orderBy('m.position', 'ASC');
				},
                'choice_label' => 'label',
            ])
            ->add('trailType', EntityType::class, [
                'label' => 'entity.Project.register.labels.trail_type',
                'class' => TrailType::class,
                'choice_label' => 'label',
            ])
            ->add('territory', EntityType::class, [
                'label' => 'entity.Project.register.labels.territory',
                'required' => true,
                'class' => Territory::class,
                'choice_label' => 'label',
                'expanded' => true,
            ])

            ->add('paymentUnit', EntityType::class, [
                'label' => 'entity.Project.register.labels.payment_unit',
                'class' => PaymentUnit::class,
                'choice_label' => 'label',
                'required' => false,
            ])

            ->add('patientNumber', EntityType::class, [
                'label' => 'entity.Project.register.labels.patient_number',
                'class' => PatientNumber::class,
                'choice_label' => 'label',
                'required' => false,
            ])

            ->add('acronyme', TextType::class, [
                'label' => 'entity.Project.register.labels.acronym',
                'attr' => [
                    'placeholder' => 'entity.Project.register.placeholders.acronym',
                ],
            ])

            ->add('nameEnglish', TextareaType::class, [
                'label' => 'entity.Project.register.labels.name_english',
                'attr' => [
                    'placeholder' => 'entity.Project.register.placeholders.name_english',
                ],
				'required' => true,
            ])

            ->add('name', TextareaType::class, [
                'label' => 'entity.Project.register.labels.name_french',
                'attr' => [
                    'placeholder' => 'entity.Project.register.placeholders.name_french',
                ],
				'required' => true,
            ])

            ->add('ref', TextType::class, [
                'label' => 'entity.Project.register.labels.ref',
                'required' => false,
                'attr' => [
                    'placeholder' => 'entity.Project.register.placeholders.ref',
                ],
            ])

            ->add('sponsor', EntityType::class, [
                'label' => 'entity.Project.field.sponsor',
                'placeholder' => 'entity.Project.field.sponsor',
                'class' => Sponsor::class,
                'choice_label' => 'label',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.label', 'ASC');
                },
                'required' => true,
            ])

            ->add('responsiblePM', EntityType::class, [
                'label' => 'entity.Project.field.responsible_pm',
                'placeholder' => 'entity.Project.field.responsible_pm',
                'class' => User::class,
                'choice_label' => 'getFullName',
                'query_builder' => function (EntityRepository $er) { // mp: laisser ça !
                    return $er->createQueryBuilder('u')
                        ->innerJoin('u.job', 'j')
                        ->where("j.code = 'CDP'")
                        ->orderBy('u.lastName', 'ASC');
                },
                'required' => true,
            ])

            ->add('responsibleCRA', EntityType::class, [
                'label' => 'entity.Project.field.responsible_cra',
                'placeholder' => 'entity.Project.field.responsible_cra',
                'class' => User::class,
                'choice_label' => 'getFullName',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->innerJoin('u.job', 'j')
                        ->where("j.code = 'CEC'")
                        ->orderBy('u.lastName', 'ASC');
                },
                'required' => true,
            ])
			->add('studyPopulation', ChoiceType::class, [
				'label' => 'entity.Project.register.labels.studyPopulation',
				'choices' => [
					Project::STUDY_POPULATION[Project::STUDY_POPULATION_PEDIATRIC] => Project::STUDY_POPULATION_PEDIATRIC,
					Project::STUDY_POPULATION[Project::STUDY_POPULATION_ADULT] => Project::STUDY_POPULATION_ADULT,
				],
				'required' => true,
				'multiple' => true,
			])
            ->add('countries', EntityType::class, [
                'label' => 'entity.Project.register.labels.countries',
                'class' => Country::class,
                'multiple' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name',
            ]);

        /*
         * CRF
         *
         * Type
         * URL
         */
        $builder->add('crfType', EntityType::class, [
            'label' => 'entity.Project.register.labels.crfType',
            'class' => CrfType::class,
            'choice_label' => 'label',
        ])
            ->add('crfUrl', TextType::class, [
                'label' => 'entity.Project.register.labels.url',
                'required' => false,
                'attr' => [
                    'placeholder' => 'entity.Project.register.placeholders.url',
                ],
            ])
            ->add('logoFile', VichImageType::class, [
                'label' => 'entity.Project.register.labels.logo',
                'required' => false,
                'allow_delete' => true,
                'download_link' => false,
                'imagine_pattern' => 'squared_thumbnail_small',
            ])
            ->add('backgroundColor', ColorType::class, [
                'label' => 'entity.Project.register.labels.backgroundColor',
                'required' => false,
            ])
            ->add('fontColor', ColorType::class, [
                'label' => 'entity.Project.register.labels.fontColor',
                'required' => false,
            ])
            ->add('coordinatingInvestigators', TextType::class, [
                'label' => 'entity.Project.register.labels.coordinating_investigators',
                'required' => false,
                'attr' => [
                    'placeholder' => 'entity.Project.register.placeholders.coordinating_investigators',
                ],
            ])
            ->add('eudractNumber', TextType::class, [
                'label' => 'entity.Project.register.labels.eudractNumber',
                'required' => false,
                'attr' => [
                    'placeholder' => 'entity.Project.register.placeholders.eudractNumber',
                ],
            ])
            ->add('nctNumber', TextType::class, [
                'label' => 'entity.Project.register.labels.nctNumber',
                'required' => false,
                'attr' => [
                    'placeholder' => 'entity.Project.register.placeholders.nctNumber',
                ],
            ])
            /*
            * Délégation de responsabilités (delegation)
            *
            * rep_sponsor
            * regulatory
            * manitoring
            * pharmacovigilance
            */
            ->add('delegation', DelegationType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'statusDisabled' => false,
        ]);
    }
}
