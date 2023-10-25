<?php

namespace App\ESM\Form;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationCorrection;
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
 * Class DeviationDeclarationType.
 */
class DeviationCorrectionType extends AbstractType
{
    private $deviation;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['deleteCorrection']) {
            $deviation = $options['deviation'];
            $this->deviation = $deviation;

            $builder
                ->add('description', TextType::class, [
                    'label' => 'entity.Deviation.DeviationCorrection.field.description',
                    'required' => true,
                ])
                ->add('realizedAt', DateTimeType::class, [
                    'label' => 'entity.Deviation.DeviationCorrection.field.realizedAt',
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
                    'label' => 'entity.Deviation.DeviationCorrection.field.applicationPlannedAt',
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
                    'label' => 'entity.Deviation.DeviationCorrection.field.efficiencyMeasure',
                    'choices' => array_flip(Deviation::EFFICIENCY_MEASURE),
                    'required' => true,
                ])
                ->add('notEfficiencyMeasureReason', TextType::class, [
                    'label' => 'entity.Deviation.DeviationCorrection.field.notEfficiencyMeasureReason',
                    'required' => false,
                    'empty_data' => null,
                ]);

            $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'onPostSubmit']);
        } else {
            $builder->add('comment', TextareaType::class, [
                'label' => 'entity.Deviation.DeviationCorrection.field.comment',
                'attr' => [
                    'placeholder' => 'entity.Deviation.DeviationCorrection.field.comment',
                ],
                'required' => true,
                'data' => '',
            ]);
        }
    }

    public function onPostSubmit(FormEvent $event): void
    {
        $deviationCorrection = $event->getData();
        $form = $event->getForm();
        $deviationGrade = $this->deviation->getGrade();

        if (Deviation::GRADE_MAJEUR === $deviationGrade || Deviation::GRADE_CRITIQUE === $deviationGrade) {
            if (null == $deviationCorrection->getEfficiencyMeasure()) {
                $form->get('efficiencyMeasure')->addError(new FormError('La mesure de l\'efficacité !'));
            }
            if ((Deviation::EFFICIENCY_MEASURE_INEFFICIENT == $deviationCorrection->getEfficiencyMeasure() || Deviation::EFFICIENCY_MEASURE_NOT_EFFICIENT == $deviationCorrection->getEfficiencyMeasure()) && !$deviationCorrection->getNotEfficiencyMeasureReason()) {
                $form->get('notEfficiencyMeasureReason')->addError(new FormError('Le champ "si non efficace, pourquoi ?" est obligatoire!'));
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => DeviationCorrection::class,
                'projectID' => null,
                'deviation' => null,
            ])
            ->setRequired(['deleteCorrection']);
    }
}
