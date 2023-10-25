<?php

namespace App\ESM\Form;

use App\ESM\Entity\Training;
use App\ESM\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

/**
 * Class TrainingType
 * @package App\ESM\Form
 */
class TrainingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'entity.Project.register.labels.user.training.title',
                'attr' => [
                    'placeholder' => 'entity.Project.register.placeholders.user.training.title',
                ],
            ])
            ->add('startedAt', DateTimeType::class, [
                'label' => 'entity.Project.register.labels.user.training.start_date',
                'attr' => [
                    'placeholder' => 'entity.Project.register.placeholders.user.training.start_date',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => true,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('startedHour', TextType::class, [
                'label' => 'entity.Project.register.labels.user.training.start_hour',
                'attr' => [
                    'placeholder' => 'entity.Project.register.placeholders.user.training.start_hour',
                    'class' => 'js-datepicker-time',
                ],
            ])
            ->add('endedAt', DateTimeType::class, [
                'label' => 'entity.Project.register.labels.user.training.ended_date',
                'attr' => [
                    'placeholder' => 'entity.Project.register.placeholders.user.training.ended_date',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => true,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('endedHour', TextType::class, [
                'label' => 'entity.Project.register.labels.user.training.ended_hour',
                'attr' => [
                    'placeholder' => 'entity.Project.register.placeholders.user.training.ended_hour',
                    'class' => 'js-datepicker-time',
                ],
            ])
            ->add('duration', TextType::class, [
                'label' => 'entity.Project.register.labels.user.training.duration',
                'attr' => [
                    'placeholder' => 'entity.Project.register.placeholders.user.training.duration',
                ],
                'required' => false,
            ])
            ->add('materialFile', VichFileType::class, [
                'label' => 'entity.Project.register.labels.user.training.material',
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Supprimer',
                'download_link' => false,
                'asset_helper' => true,
            ])
            ->add('teacher', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'getFullName',
                'required' => true,
                'label' => 'entity.Training.field.former',
                'placeholder' => 'entity.Training.field.former',
                'query_builder' => function (EntityRepository $er) use ($options) {
					if ($options['data']->getId()) {
						return $er->createQueryBuilder('u')
							->innerJoin('u.userProjects', 'up')
							->where('up.project = :project')
							->andWhere('u.id in (:users)')
							->orWhere('up.disabledAt IS NULL')
							->setParameter('users', $options['users'])
							->setParameter('project', $options['project'])
							->orderBy('u.lastName', 'ASC');
					} else {
						return $er->createQueryBuilder('u')
							->innerJoin('u.userProjects', 'up')
							->where('up.project = :project')
							->andWhere('up.disabledAt IS NULL')
							->setParameter('project', $options['project'])
							->orderBy('u.lastName', 'ASC');

					}

                },
            ])
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'getFullName',
                'required' => true,
                'multiple' => true,
                'expanded' => false,
                'label' => 'entity.Project.user.user_list',
                'query_builder' => function (EntityRepository $er) use ($options) {
					if ($options['data']->getId()) {
						return $er->createQueryBuilder('u')
							->innerJoin('u.userProjects', 'up')
							->where('up.project = :project')
							->andWhere('u.id in (:users)')
							->orWhere('up.disabledAt IS NULL')
							->setParameter('users', $options['users'])
							->setParameter('project', $options['project'])
							->orderBy('u.lastName', 'ASC');
					} else {
						return $er->createQueryBuilder('u')
							->innerJoin('u.userProjects', 'up')
							->where('up.project = :project')
							->andWhere('up.disabledAt IS NULL')
							->setParameter('project', $options['project'])
							->orderBy('u.lastName', 'ASC');
					}
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Training::class,
        ]);
        $resolver->setRequired(['project', 'users']);
    }
}
