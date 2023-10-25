<?php

declare(strict_types=1);

namespace App\ESM\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class LoginFormType.
 */
class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'attr' => ['autofocus' => true, 'placeholder' => 'entity.User.field.email'],
                'label' => false,
            ])
            ->add('password', PasswordType::class, [
                'attr' => ['placeholder' => 'entity.User.field.password'],
                'label' => false,
            ])
            ;
    }
}
