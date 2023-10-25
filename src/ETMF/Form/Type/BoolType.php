<?php

namespace App\ETMF\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class BoolType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return CheckboxType::class;
    }
}
