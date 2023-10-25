<?php

namespace App\ETMF\Form;

use App\ETMF\Entity\Artefact;
use App\ETMF\Entity\DropdownList\ArtefactLevel;
use App\ETMF\Entity\Mailgroup;
use App\ETMF\Entity\Section;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ArtefactType
 * @package App\ETMF\Form
 */
class ArtefactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('name', TextType::class, [
                'label' => 'etmf.artefact.field.name',
                'attr' => [
                    'placeholder' => 'nom de l\'artefact',
                ],
                'required' => true,
            ])

            ->add('code', TextType::class, [
                'label' => 'etmf.artefact.field.number',
                'attr' => [
                    'placeholder' => 'numéro de l\'artefact',
                    'pattern' => '\d+',
                ],
                'required' => true,
            ])

            ->add('section', EntityType::class, [
                'label' => 'etmf.artefact.field.section',
                'class' => Section::class,
                'choice_label' => 'name',
                'required' => true
            ])

            ->add('artefactLevels', EntityType::class, [
                'class' => ArtefactLevel::class,
                'label' => 'etmf.artefact.field.level',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('artefactLevel')
                        ->orderBy('artefactLevel.level', 'ASC');
                },
                'choice_label' => 'level',
                'multiple' => true,
                'required' => false,
            ])

            ->add('extension', ChoiceType::class, [
                'label' => 'etmf.artefact.field.extension',
                'choices' => [
                    'choices' => array_flip(Artefact::EXTENSIONS),
                ],
                'required' => false,
                'multiple' => true,
            ])

            ->add('mailgroups', EntityType::class, [
                'class' => Mailgroup::class,
                'label' => 'etmf.artefact.field.mailgroups',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('mailgroup')
                        ->orderBy('mailgroup.name', 'ASC');
                },
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
            ])

            ->add('delayExpired', TextType::class, [
                'label' => 'etmf.artefact.field.expired',
                'attr' => [
                    'placeholder' => 'en jour(s)',
                    'pattern' => '\d+',
                ],
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Artefact::class,
        ]);
    }
}
