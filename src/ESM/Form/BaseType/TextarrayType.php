<?php

namespace App\ESM\Form\BaseType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TextarrayType
 * @package App\ESM\Form\BaseType
 */
class TextarrayType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return CollectionType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => function ($str = null) {
                return empty($str);
            },
        ]);
        $resolver->setRequired([
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
    }
}
