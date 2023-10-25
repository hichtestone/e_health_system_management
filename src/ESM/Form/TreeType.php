<?php

namespace App\ESM\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TreeType
 * @package App\ESM\Form
 */
class TreeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return EntityType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'multiple' => true, // ne pas overrider (js dépendant)
            'expanded' => true, // ne pas overrider (js dépendant)
            'attr' => [
                'jstree_show_all' => true, // true pour tout dérouler
            ],
        ]);
        $resolver->setRequired([]);
    }
}
