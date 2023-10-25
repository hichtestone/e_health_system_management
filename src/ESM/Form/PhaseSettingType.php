<?php

namespace App\ESM\Form;

use App\ESM\Entity\PhaseSetting;
use App\ESM\Entity\PhaseSettingStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PhaseSettingType
 * @package App\ESM\Form
 */
class PhaseSettingType extends AbstractType
{
	protected $translator;

	public function __construct(TranslatorInterface $translator)
	{
		$this->translator = $translator;
	}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('ordre', NumberType::class, [
                'label' => 'entity.PhaseSetting.field.order',
                'attr' => [
                    'placeholder' => 'entity.PhaseSetting.field.order',
                    'pattern' => '\d+',
                ],
                'required' => false,
            ])

            ->add('label', TextType::class, [
                'label' => 'entity.PhaseSetting.field.label',
                'attr' => [
                    'placeholder' => 'entity.PhaseSetting.field.label',
                ],
                'required' => true,
            ])

			->add('phaseSettingStatus', EntityType::class, [
				'class' => PhaseSettingStatus::class,
				'label' => 'entity.PhaseSetting.field.status',
				'attr' => [
					'placeholder' => 'entity.PhaseSetting.field.status',
				],
				'required' => true,
				'choice_label' => function ($phaseSettingStatus, $value) {
					return $this->translator->trans($phaseSettingStatus->getLabel());
				}
			])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PhaseSetting::class,
        ]);
    }
}
