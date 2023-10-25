<?php

namespace App\ESM\Form;

use App\ESM\Entity\Center;
use App\ESM\Entity\Patient;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PatientType
 * @package App\ESM\Form
 */
class PatientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('patient_number', TextType::class, [
                'label' => 'entity.PatientTracking.field.patient_number',
                'attr' => [
                    'placeholder' => 'entity.PatientTracking.field.patient_number',
                ],
                'required' => true,
            ])
            ->add('randoGroup')
            ->add('randoAt', DateTimeType::class, [
                'label' => 'entity.PatientTracking.field.randoAt',
                'attr' => [
                    'placeholder' => 'dd/mm/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('consentAt', DateTimeType::class, [
                'label' => 'entity.PatientTracking.field.consentAt',
                'attr' => [
                    'placeholder' => 'dd/mm/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('enrolledAt', DateTimeType::class, [
                'label' => 'entity.PatientTracking.field.enrolledAt',
                'attr' => [
                    'placeholder' => 'dd/mm/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            /*->add('endOfStudyAt', DateTimeType::class, [
                'label' => 'entity.PatientTracking.field.endOfStudyAt',
                'attr' => [
                    'placeholder' => 'dd/mm/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])*/
            ->add('center', EntityType::class, [
                'label' => 'entity.PatientTracking.field.center_number',
                'class' => Center::class,
                'required' => false,
                'placeholder' => 'Sélectionnez le numéro du centre',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('c')
                        ->innerJoin('c.project', 'p')
                        ->where('p.id = :id')
                        ->setParameter('id', $options['project']->getId());
                },
                'choice_label' => 'displayNameNumber',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => Patient::class,
            ])
            ->setRequired('project');
    }
}
