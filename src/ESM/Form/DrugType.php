<?php

namespace App\ESM\Form;

use App\ESM\Entity\DropdownList\TrailTreatment;
use App\ESM\Entity\Drug;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DrugType
 * @package App\ESM\Form
 */
class DrugType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'entity.Drug.field.name',
                'attr' => [
                    'placeholder' => 'entity.Drug.field.name',
                ],
                'required' => true,
            ])
            ->add('TrailTreatment', EntityType::class, [
                'label' => 'entity.Drug.field.TreatmentType',
                'class' => TrailTreatment::class,
                'choice_label' => 'label',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Drug::class,
            'role_access' => false,
        ]);
    }
}
