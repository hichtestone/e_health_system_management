<?php

namespace App\ESM\Form;

use App\ESM\Entity\Institution;
use App\ESM\Entity\Interlocutor;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class InterlocutorInstitutionMassType
 * @package App\ESM\Form
 */
class InterlocutorInstitutionMassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('institutions', EntityType::class, [
                'mapped' => false,
                'label' => 'entity.Institution.action.list',
                'class' => Institution::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('i')
                        ->where('i.deletedAt IS NULL')
                        ->orderBy('i.name', 'ASC');
                },
                'choice_label' => 'name',
                'required' => true,
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('interlocutors', EntityType::class, [
                'mapped' => false,
                'class' => Interlocutor::class,
                'choice_label' => 'lastName',
                'required' => true,
                'multiple' => true,
                'expanded' => false,
                'attr' => [
                    'hidden' => true,
                    'class' => 'd-none',
                ],
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
