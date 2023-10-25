<?php

namespace App\ESM\Form;

use App\ESM\Entity\Delegation;
use App\ESM\Form\BaseType\BoolType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DelegationType
 * @package App\ESM\Form
 */
class DelegationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('repSponsor', TextType::class, [
                'label' => 'entity.Project.register.labels.rep_sponsor',
                'required' => false,
                'attr' => [
                    'placeholder' => 'entity.Project.register.placeholders.rep_sponsor',
                ],
            ])
            ->add('regulatory', BoolType::class, [
                'label' => 'entity.Project.register.labels.regulatory',
                'required' => false,
            ])
            ->add('manitoring', BoolType::class, [
                'label' => 'entity.Project.register.labels.manitoring',
                'required' => false,
            ])
            ->add('pharmacovigilance', BoolType::class, [
                'label' => 'entity.Project.register.labels.pharmacovigilance',
                'required' => false,
            ])
            ->add('dsur', BoolType::class, [
                'label' => 'DSURs',
                'required' => false,
            ])
            ->add('susar', BoolType::class, [
                'label' => 'SUSARs',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Delegation::class,
        ]);
    }
}
