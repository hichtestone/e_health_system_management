<?php

namespace App\ESM\Form;

use App\ESM\Entity\Project;
use App\ESM\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserProjectMassType
 * @package App\ESM\Form
 */
class UserProjectMassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('projects', EntityType::class, [
                'mapped' => false,
                'label' => 'entity.Project.list.header',
                'class' => Project::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.closedAt IS NULL')
                        ->orderBy('p.acronyme', 'ASC');
                },
                'choice_label' => 'acronyme',
                'required' => true,
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('users', EntityType::class, [
                'mapped' => false,
                'class' => User::class,
                'choice_label' => 'email',
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
    }
}
