<?php

namespace App\ESM\Form;

use App\ESM\Entity\Meeting;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UsersMeetingMassType
 * @package App\ESM\Form
 */
class UsersMeetingMassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('meetings', EntityType::class, [
                'mapped' => false,
                'label' => 'entity.Project.user.list.header',
                'class' => Meeting::class,
                'choice_label' => 'title',
                'required' => true,
                'multiple' => true,
                'expanded' => true,
            ])
            /*->add('projets', EntityType::class, [
                'mapped' => false,
                'label' => 'entity.Project.user.list.header',
                'class' => Project::class,
                'choice_label' => 'name',
                'required' => true,
                'multiple' => true,
                'expanded' => true,
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
