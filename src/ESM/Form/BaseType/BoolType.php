<?php

namespace App\ESM\Form\BaseType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BoolType
 * @package App\ESM\Form\BaseType
 */
class BoolType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return CheckboxType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
        ]);
        parent::configureOptions($resolver);
    }
}
