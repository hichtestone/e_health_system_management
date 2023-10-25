<?php

namespace App\ESM\Form;

use App\ESM\Entity\DropdownList\Civility;
use App\ESM\Entity\DropdownList\Department;
use App\ESM\Entity\DropdownList\Society;
use App\ESM\Entity\DropdownList\UserJob;
use App\ESM\Entity\Profile;
use App\ESM\Entity\User;
use App\ESM\Form\BaseType\BoolType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserType
 * @package App\ESM\Form
 */
class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('civility', EntityType::class, [
                'label' => 'entity.User.register.labels.civility',
                'required' => true,
                'class' => Civility::class,
                'choice_label' => 'label',
            ])
            ->add('email', TextType::class, [
                'label' => 'entity.User.register.labels.email',
                'attr' => [
                    'placeholder' => 'entity.User.register.placeholders.email',
                ],
                'required' => true,
            ])
            ->add('phone', TextType::class, [
                'label' => 'entity.User.register.labels.phone',
                'attr' => [
                    'placeholder' => 'entity.User.register.placeholders.phone',
                ],
                'required' => true,
            ])
            ->add('society', EntityType::class, [
                'label' => 'entity.User.register.labels.society',
                'required' => true,
                'class' => Society::class,
                'choice_label' => 'name',
            ])
            ->add('note', TextareaType::class, [
                'label' => 'entity.User.register.labels.note',
                'attr' => [
                    'placeholder' => 'entity.User.register.placeholders.note',
                ],
                'required' => false,
            ])
            ->add('job', EntityType::class, [
                'label' => 'entity.User.register.labels.job',
                'class' => UserJob::class,
                'choice_label' => 'label',
                'required' => true,
            ])
            ->add('department', EntityType::class, [
                'label' => 'entity.User.register.labels.department',
                'class' => Department::class,
                'choice_label' => 'label',
                'required' => false,
            ])
            ->add('firstName', TextType::class, [
                'label' => 'entity.User.register.labels.firstName',
                'attr' => [
                    'placeholder' => 'entity.User.register.placeholders.firstName',
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'entity.User.register.labels.lastName',
                'attr' => [
                    'placeholder' => 'entity.User.register.placeholders.lastName',
                ],
                'required' => true,
            ])
            ->add('profile', EntityType::class, [
                'label' => 'Profil',
                'class' => Profile::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.deletedAt IS NULL')
                        ->orderBy('p.name', 'ASC');
                },
            ])
            ;
        if ($options['role_access']) {
            $builder->add('hasAccessEsm', BoolType::class, [
                'label' => 'entity.User.register.labels.hasAccessEsm',
                'required' => false,
            ])
                ->add('hasAccessEtmf', BoolType::class, [
                    'label' => 'entity.User.register.labels.hasAccessEtmf',
                    'required' => false,
                ])
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'role_access' => false,
        ]);
    }
}
