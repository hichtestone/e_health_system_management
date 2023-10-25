<?php

namespace App\ESM\Form;

use App\ESM\Entity\DocumentTracking;
use App\ESM\Entity\DropdownList\Country;
use App\ESM\Form\BaseType\BoolType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DocumentTrackingType
 * @package App\ESM\Form
 */
class DocumentTrackingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'entity.DocumentTracking.field.name',
                'attr' => [
                    'placeholder' => 'entity.DocumentTracking.field.name',
                ],
            ])
            ->add('version', TextType::class, [
                'label' => 'entity.DocumentTracking.field.version',
                'attr' => [
                    'placeholder' => 'entity.DocumentTracking.field.version',
                ],
            ])
            ->add('country', EntityType::class, [
                'label' => 'entity.DocumentTracking.field.country',
                'class' => Country::class,
                'choice_label' => 'name',
                'placeholder' => '<<Pays>>',
                'required' => false,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('c')
                        ->innerJoin('c.projects', 'p')
                        ->where('p = :project')
                        ->setParameter('project', $options['project'])
                        ;
                },
                'disabled' => $options['hasTrackings'],
            ])
        ;

        $builder->add('level', ChoiceType::class, [
            'label' => 'entity.DocumentTracking.field.scope',
            'choices' => [
                'Centre' => DocumentTracking::levelCenter,
                'Interlocuteur' => DocumentTracking::levelInterlocutor,
            ],
            'disabled' => $options['hasTrackings'],
        ]);

        $builder->add('toBeSent', BoolType::class, [
                'label' => 'entity.DocumentTracking.field.toBeSent',
            ])
            ->add('toBeReceived', BoolType::class, [
                'label' => 'entity.DocumentTracking.field.toBeReceived',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DocumentTracking::class,
        ]);
        $resolver->setRequired('project');
        $resolver->setRequired('hasTrackings');
    }
}
