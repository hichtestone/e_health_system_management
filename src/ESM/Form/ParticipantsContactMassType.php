<?php

namespace App\ESM\Form;

use App\ESM\Entity\Contact;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ParticipantsContactMassType
 * @package App\ESM\Form
 */
class ParticipantsContactMassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contacts', EntityType::class, [
                'mapped' => false,
                'label' => 'entity.Project.participant.contact.list.header',
                'class' => Contact::class,
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
