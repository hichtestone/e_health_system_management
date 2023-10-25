<?php

namespace App\ESM\Form;

use App\ESM\Entity\Service;
use App\ESM\Form\BaseType\BoolType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ServiceType
 * @package App\ESM\Form
 */
class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'entity.Service.field.name',
            ])
            ->add('addressInherited', BoolType::class, [
                'label' => 'entity.Service.field.address_inherited',
                'required' => false,
            ])
            ->add('address', TextType::class, [
                'label' => 'entity.Service.field.address',
                'required' => false,
            ])
            ->add('address2', TextType::class, [
                'label' => 'entity.Service.field.address2',
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'label' => 'entity.Service.field.city',
                'required' => false,
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'entity.Service.field.postal_code',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);

        $resolver->setRequired('institution');
    }
}
