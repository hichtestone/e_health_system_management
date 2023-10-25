<?php

declare(strict_types=1);

namespace App\ESM\Form\Security;

use App\ESM\Validator\Constraints\ComplexPattern;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class LoginFormType.
 */
class ResetPasswordType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password_current', PasswordType::class, [
                'attr' => ['autofocus' => true],
                'label' => $this->translator->trans('security.password.current'),
            ])
            ->add('password_new', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => $this->translator->trans('security.password.bad_confirm'),
                'first_options' => ['label' => $this->translator->trans('security.password.new')],
                'second_options' => ['label' => $this->translator->trans('security.password.confirm')],
                'constraints' => [
                    new ComplexPattern([
                        'regexValid' => ['.{8,}'],
                        'regexInvalid' => ['^[A-Z]+$', '^[^A-Z]+$', '^[^\d]+$', '^[^&#\([@\]\)\$£%\*µ,\?;\.:\/!§²]+$'],
                        'message' => $this->translator->trans('security.password.constraints'),
                    ]),
                ],
            ])
            ;
    }
}
