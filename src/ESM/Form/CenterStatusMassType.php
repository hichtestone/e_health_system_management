<?php

namespace App\ESM\Form;

use App\ESM\Entity\Center;
use App\ESM\Entity\DropdownList\CenterStatus;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CenterStatusMassType
 * @package App\ESM\Form
 */
class CenterStatusMassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('centerStatus', EntityType::class, [
                'label' => 'entity.Center.field.centerStatus',
                'class' => CenterStatus::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('c')
                        ->where('c.type IN (:type)')
                        ->setParameter('type', $options['status_type'])
                        ->orderBy('c.position', 'ASC');
                },
                'choice_label' => 'label',
                'required' => true,
                'placeholder' => 'Veuillez choisir un statut',
            ])
            ->add('centers', EntityType::class, [
                'mapped' => false,
                'class' => Center::class,
                'choice_label' => 'name',
                'required' => true,
                'multiple' => true,
                'expanded' => false,
                'attr' => [
                    'hidden' => true,
                    'class' => 'd-none',
                ],
                'label' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
        $resolver->setRequired('status_type');
    }
}
