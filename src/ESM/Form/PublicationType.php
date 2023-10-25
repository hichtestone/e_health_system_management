<?php

namespace App\ESM\Form;

use App\ESM\Entity\DropdownList\CommunicationType;
use App\ESM\Entity\DropdownList\Congress;
use App\ESM\Entity\DropdownList\IsCongress;
use App\ESM\Entity\DropdownList\Journals;
use App\ESM\Entity\DropdownList\PostType;
use App\ESM\Entity\Publication;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PublicationType
 * @package App\ESM\Form
 */
class PublicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('communicationType', EntityType::class, [
                'label' => 'entity.Project.publication.register.labels.communicationType',
                'class' => CommunicationType::class,
                'choice_label' => 'label',
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.label', 'ASC');
                },
            ])
            ->add('isCongress', EntityType::class, [
                'label' => 'entity.Project.publication.register.labels.isCongress',
                'class' => IsCongress::class,
                'choice_label' => 'label',
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.label', 'ASC');
                },
            ])
            ->add('journals', EntityType::class, [
                'label' => 'entity.Project.publication.register.labels.name',
                'class' => Journals::class,
                'choice_label' => 'label',
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.label', 'ASC');
                },
            ])
            ->add('congress', EntityType::class, [
                'label' => 'entity.Project.publication.register.labels.name',
                'class' => Congress::class,
                'choice_label' => 'label',
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.label', 'ASC');
                },
            ])
            ->add('postType', EntityType::class, [
                'label' => 'entity.Project.publication.register.labels.postType',
                'class' => PostType::class,
                'choice_label' => 'label',
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.label', 'ASC');
                },
            ])
            ->add('comment', TextType::class, [
                    'label' => 'entity.Project.publication.register.labels.comment',
                    'attr' => [
                        'placeholder' => 'entity.Project.publication.register.placeholders.comment',
                    ],
                    'required' => false,
            ])
            ->add('postNumber', TextType::class, [
                'label' => 'entity.Project.publication.register.labels.postNumber',
                'attr' => [
                    'placeholder' => 'entity.Project.publication.register.placeholders.postNumber',
                ],
                'required' => true,
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'entity.Project.publication.register.labels.postAt',
                'attr' => [
                    'placeholder' => 'entity.Project.publication.register.placeholders.postAt',
                    'class' => 'js-datepicker journals',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => true,
                'format' => 'dd/MM/yyyy',
            ])

            ->add('date_year', NumberType::class, [
                'label' => 'Année du congrès',
                'attr' => ['min' => date('Y')],
                'required' => true,
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Publication::class,
        ]);
    }
}
