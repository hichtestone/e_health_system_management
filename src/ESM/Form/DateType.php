<?php

namespace App\ESM\Form;

use App\ESM\Entity\Date;
use App\ESM\Form\BaseType\BoolType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DateType
 * @package App\ESM\Form
 */
class DateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('submissionAt', DateTimeType::class, [
                'label' => 'entity.Date.field.submissionAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('feasibilityCommitteeAt', DateTimeType::class, [
                'label' => 'entity.Date.field.feasibilityCommitteeAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('reviewCommitteeAt', DateTimeType::class, [
                'label' => 'entity.Date.field.reviewCommitteeAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('registrationAt', DateTimeType::class, [
                'label' => 'entity.Date.field.registrationAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('subscriptionStartedAt', DateTimeType::class, [
                'label' => 'entity.Date.field.subscriptionStartedAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('subscriptionEndedAt', DateTimeType::class, [
                'label' => 'entity.Date.field.subscriptionEndedAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('certificationAt', DateTimeType::class, [
                'label' => 'entity.Date.field.certificationAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('kiffOfMeetingAt', DateTimeType::class, [
                'label' => 'entity.Date.field.kiffOfMeetingAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('mepForecastAt', DateTimeType::class, [
                'label' => 'entity.Date.field.mepForecastAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('mepAt', DateTimeType::class, [
                'label' => 'entity.Date.field.mepAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('forecastInclusionStartedAt', DateTimeType::class, [
                'label' => 'entity.Date.field.forecastInclusionStartedAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('firstConsentAt', DateTimeType::class, [
                'label' => 'entity.Date.field.firstConsentAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('inclusionPatientStartedAt', DateTimeType::class, [
                'label' => 'entity.Date.field.inclusionPatientStartedAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('forecastInclusionEndedAt', DateTimeType::class, [
                'label' => 'entity.Date.field.forecastInclusionEndedAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('inclusionPatientEndedAt', DateTimeType::class, [
                'label' => 'entity.Date.field.inclusionPatientEndedAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('studyDeclarationEndedAt', DateTimeType::class, [
                'label' => 'entity.Date.field.studyDeclarationEndedAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('validationCommmitteeReviewEndedAt', DateTimeType::class, [
                'label' => 'entity.Date.field.validationCommmitteeReviewEndedAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('amendmentsBeforFirstInclusion', BoolType::class, [
                    'label' => 'entity.Date.field.amendmentsBeforFirstInclusion',
            ])
            ->add('numberExpectedPatients', TextType::class, [
                'label' => 'entity.Date.field.numberExpectedPatients',
                'required' => true,
            ])
            ->add('numberScreenedPatients', TextType::class, [
                'label' => 'entity.Date.field.numberScreenedPatients',
                'required' => false,
            ])
            ->add('numberPatientsIncluded', TextType::class, [
                'label' => 'entity.Date.field.numberPatientsIncluded',
                'required' => true,
            ])
            ->add('numberRandomizedPatients', TextType::class, [
                'label' => 'entity.Date.field.numberRandomizedPatients',
                'required' => false,
            ])
            ->add('expectedDurationInclusionAt', NumberType::class, [
                'label' => 'entity.Date.field.expectedDurationInclusionAt',
                'attr' => [
                    'placeholder' => 'durée (en mois)',
					'pattern' => '\d+',
                ],
                'required' => true,
            ])
            ->add('expectedDurationFollowUpAfterInclusionAt', NumberType::class, [
                'label' => 'entity.Date.field.expectedDurationFollowUpAfterInclusionAt',
                'attr' => [
                    'placeholder' => 'durée (en mois)',
					'pattern' => '\d+',
                ],
                'required' => true,
            ])
            ->add('expectedLPLVAt', DateTimeType::class, [
                'label' => 'entity.Date.field.expectedLPLVAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('actualLPLVAt', DateTimeType::class, [
                'label' => 'entity.Date.field.actualLPLVAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('expectedReportAnalysedAt', DateTimeType::class, [
                'label' => 'entity.Date.field.expectedReportAnalysedAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('actualReportAnalysedtAt', DateTimeType::class, [
                'label' => 'entity.Date.field.actualReportAnalysedtAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('finalReportAnalysedAt', DateTimeType::class, [
                'label' => 'entity.Date.field.finalReportAnalysedAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('finalExpectedLPLVAt', DateTimeType::class, [
                'label' => 'entity.Date.field.finalExpectedLPLVAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('finalActualLPLVAt', DateTimeType::class, [
                'label' => 'entity.Date.field.finalActualLPLVAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('finalExpectedReportAt', DateTimeType::class, [
                'label' => 'entity.Date.field.finalExpectedReportAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('finalActualReportAt', DateTimeType::class, [
                'label' => 'entity.Date.field.finalActualReportAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('finalActualReportAt', DateTimeType::class, [
                'label' => 'entity.Date.field.finalActualReportAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('finalActualReportClinicalAt', DateTimeType::class, [
                'label' => 'entity.Date.field.finalActualReportClinicalAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('depotClinicalTrialsAt', DateTimeType::class, [
                'label' => 'entity.Date.field.depotClinicalTrialsAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('depotEudraCtAt', DateTimeType::class, [
                'label' => 'entity.Date.field.depotEudraCtAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Date::class,
        ]);
    }
}
