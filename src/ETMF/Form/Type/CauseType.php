<?php

namespace App\ETMF\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CauseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('causalite', ChoiceType::class, [
            'choices'  => [
                'Humaine ;Equipement' => null,
                'Yes' => true,
                'No' => false,
            ],
        ]);
        $builder->add('grade', ChoiceType::class, [
            'choices'  => [
                'Critique' => null,
                'Yes' => true,
                'No' => false,
            ],
        ]);

        $builder->add('DescriptionC', TextType::class, [
            'label' => 'Description de la causalité'

        ]);
    }
}
