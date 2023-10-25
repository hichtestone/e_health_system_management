<?php

declare(strict_types=1);

namespace App\ESM\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class TwoFactorType.
 */
class TwoFactorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'security.two_factor.otp_code',
            ])
        ;
    }
}
