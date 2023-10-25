<?php

namespace App\ESM\Form;

use App\ESM\Entity\ReportBlockParam;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ReportBlockParamType
 * @package App\ESM\Form
 */
class ReportBlockParamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['editParam']) {
            $builder->add('param', TextareaType::class, [
                'label' => 'Paramétre',
                'attr' => [
                    'placeholder' => 'Paramétre',
                ],
                'required' => true,
            ]);
        } else {
            $builder->add('ordering')
                ->add('block');
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => ReportBlockParam::class,
            ])
            ->setRequired(['editParam']);
    }
}
