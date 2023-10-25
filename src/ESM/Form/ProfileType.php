<?php

namespace App\ESM\Form;

use App\ESM\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ProfileType
 * @package App\ESM\Form
 */
class ProfileType extends AbstractType
{
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('name', TextType::class, [
                'label' => 'entity.Profile.register.labels.name',
                'constraints' => new Length(['min' => 3, 'max' => 30]),
                'attr' => ['placeholder' => 'entity.Profile.register.placeholders.name'],
            ])

            ->add('acronyme', TextType::class, [
                'label' => 'entity.Profile.register.labels.acronyme',
                'constraints' => new Length(['min' => 2, 'max' => 15]),
                'attr' => ['placeholder' => 'entity.Profile.register.placeholders.acronyme'],
            ])

			->add('type', ChoiceType::class, [
				'placeholder' => 'Entrez un type de compte pour le profil',
				'label'		  => 'Type de compte',
				'required' 	  => true,
				'choices' 	  => array_flip(Profile::PROFILS_TYPE),
			])

            ->add('roles', null, [
                'label' => $this->translator->trans('entity.Role.label', ['%count%' => 2]),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
        ]);
    }
}
