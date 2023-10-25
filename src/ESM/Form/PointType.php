<?php

namespace App\ESM\Form;

use App\ESM\Entity\Point;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PointType
 * @package App\ESM\Form
 */
class PointType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('x')
            ->add('y');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Point::class,
            'attr' => [
                'class' => 'row',
            ],
        ]);
    }
}
