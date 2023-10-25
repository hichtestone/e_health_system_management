<?php

namespace App\ESM\Form;

use App\ESM\Entity\Training;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UsersTrainingMassType
 * @package App\ESM\Form
 */
class UsersTrainingMassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('trainings', EntityType::class, [
                'mapped' => false,
                'label' => 'entity.Project.user.list.header',
                'class' => Training::class,
                'choice_label' => 'title',
                'required' => true,
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
