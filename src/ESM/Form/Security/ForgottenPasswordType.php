<?php

declare(strict_types=1);

namespace App\ESM\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ForgettenPassword.
 */
class ForgottenPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'entity.User.field.email',
            ])
        ;
    }
}
