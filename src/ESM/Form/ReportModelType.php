<?php

namespace App\ESM\Form;

use App\ESM\Entity\ReportModel;
use App\ESM\Validator\UnitReportModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ReportModelType
 * @package App\ESM\Form
 */
class ReportModelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reportType', ChoiceType::class, [
                'label' => 'entity.report_model.field.report_type.name',
                'choices' => array_flip(ReportModel::REPORT_TYPE),
                'required' => true,
            ])
            ->add('visitType', ChoiceType::class, [
                'label' => 'entity.report_model.field.visit_type.name',
                'choices' => array_flip(ReportModel::VISIT_TYPE),
                'required' => true,
            ])
            ->add('name', TextType::class, [
                'label' => 'entity.report_model.field.name',
                'attr' => [
                    'placeholder' => 'entity.report_model.field.name',
                ],
                'required' => true,
                'constraints' => [
                    new UnitReportModel(),
                ],
            ])
            ->add('idCurrent', HiddenType::class, [
                'mapped' => false,
                'data' => $options['data']->getId() ?? null,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ReportModel::class,
        ]);
    }
}
