<?php

namespace App\ESM\Form;

use App\ESM\Entity\DropdownList\Civility;
use App\ESM\Entity\DropdownList\Cooperator;
use App\ESM\Entity\DropdownList\ParticipantJob;
use App\ESM\Entity\DropdownList\ParticipantSpeciality;
use App\ESM\Entity\Institution;
use App\ESM\Entity\Interlocutor;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class InterlocutorType
 * @package App\ESM\Form
 */
class InterlocutorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('civility', EntityType::class, [
                'label' => 'entity.Interlocutor.field.civility',
                'class' => Civility::class,
                'choice_label' => 'label',
            ])

            ->add('firstName', TextType::class, [
                'label' => 'entity.Interlocutor.field.firstName',
                'attr' => [
                    'placeholder' => 'entity.Interlocutor.field.firstName',
                ],
            ])

            ->add('lastName', TextType::class, [
                'label' => 'entity.Interlocutor.field.lastName',
                'attr' => [
                    'placeholder' => 'entity.Interlocutor.field.lastName',
                ],
            ])

            ->add('job', EntityType::class, [
                'label' => 'entity.Interlocutor.field.job',
                'class' => ParticipantJob::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('j')
                        ->orderBy('j.label', 'ASC');
                },
                'choice_label' => 'label',
            ])

            ->add('rppsNumber', TextType::class, [
                'label' => 'entity.Interlocutor.field.rppsNumber',
                'attr' => [
                    'placeholder' => 'entity.Interlocutor.field.rppsNumber',
                    'maxlength' => 11,
                    'pattern' => '\d+',
                ],
                'required' => false,
            ])

            ->add('specialtyOne', EntityType::class, [
                'label' => 'entity.Interlocutor.field.specialtyOne',
                'class' => ParticipantSpeciality::class,
                'choice_label' => 'label',
                'required' => false,
            ])

            ->add('specialtyTwo', EntityType::class, [
                'label' => 'entity.Interlocutor.field.specialtyTwo',
                'class' => ParticipantSpeciality::class,
                'choice_label' => 'label',
                'required' => false,
            ])

			->add('cooperators', EntityType::class, [
				'class' => Cooperator::class,
				'choice_label' => 'title',
				'required' => false,
				'multiple' => true,
				'label' => 'entity.Interlocutor.field.cooperator',
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder('cooperator')
						->where('cooperator.deletedAt IS NULL')
						->orderBy('cooperator.title', 'ASC');
				},
			])

            ->add('phone', TextType::class, [
                'label' => 'entity.Interlocutor.field.phone',
                'attr' => [
                    'placeholder' => 'entity.Interlocutor.field.phone',
                ],
                'required' => false,
            ])

            ->add('fax', TextType::class, [
                'label' => 'entity.Interlocutor.field.fax',
                'attr' => [
                    'placeholder' => 'entity.Interlocutor.field.fax',
                ],
                'required' => false,
            ])

            ->add('email', EmailType::class, [
                'label' => 'entity.Interlocutor.field.email',
                'attr' => [
                    'placeholder' => 'entity.Interlocutor.field.email',
                ],
                'required' => true,
            ])

            ->add('institutions', EntityType::class, [
                'class' => Institution::class,
                'choice_label' => 'name',
                'required' => true,
                'multiple' => true,
                'label' => 'entity.Institution.action.list',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('institution')
                        ->where('institution.deletedAt IS NULL')
                        ->orderBy('institution.name', 'ASC');
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Interlocutor::class,
        ]);
    }
}
