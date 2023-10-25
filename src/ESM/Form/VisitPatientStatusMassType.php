<?php

namespace App\ESM\Form;

use App\ESM\Entity\VisitPatient;
use App\ESM\Entity\VisitPatientStatus;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class VisitPatientStatusMassType
 * @package App\ESM\Form
 */
class VisitPatientStatusMassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', EntityType::class, [
                'label' => 'entity.VisitPatient.field.status',
                'class' => VisitPatientStatus::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.label', 'ASC');
                },
                'choice_label' => 'label',
                'required' => true,
                'placeholder' => 'Veuillez choisir un statut',
            ])
            ->add('visitPatients', EntityType::class, [
                'mapped' => false,
                'class' => VisitPatient::class,
                'choice_label' => 'displayVisitPatient',
                'required' => true,
                'multiple' => true,
                'expanded' => false,
                'attr' => [
                    'hidden' => true,
                    'class' => 'd-none',
                ],
                'label' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
