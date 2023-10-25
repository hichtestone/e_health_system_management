<?php

namespace App\ESM\Form;

use App\ESM\Entity\Meeting;
use App\ESM\Entity\User;
use App\ESM\Validator\IntegerDurationMeeting;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

/**
 * Class MeetingType
 * @package App\ESM\Form
 */
class MeetingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', TextType::class, [
                'label' => 'entity.Project.register.labels.user.meeting.type',
                'attr' => [
                    'placeholder' => 'entity.Project.register.placeholders.user.meeting.type',
                ],
            ])
            ->add('startedAt', DateTimeType::class, [
                'label' => 'entity.Project.register.labels.user.meeting.date',
                'attr' => [
                    'placeholder' => 'entity.Project.register.placeholders.user.meeting.date',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => true,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('startedHour', TextType::class, [
                'label' => 'entity.Project.register.labels.user.meeting.hour',
                'attr' => [
                    'placeholder' => 'entity.Project.register.placeholders.user.meeting.hour',
                    'class' => 'js-datepicker-time',
                ],
            ])
            ->add('duration', TextType::class, [
                'label' => 'entity.Project.register.labels.user.meeting.duration',
                'attr' => [
                    'placeholder' => 'entity.Project.register.placeholders.user.meeting.duration',
                ],
                'required' => false,
				'constraints' => [
					new IntegerDurationMeeting(),
				],
            ])
            ->add('object', EntityType::class, [
                'label' => 'entity.Project.register.labels.user.meeting.object',
                'class' => \App\ESM\Entity\DropdownList\MeetingType::class,
                'choice_label' => 'label',
            ])
			->add('reportFile', VichFileType::class, [
			'label' => 'entity.Project.register.labels.user.meeting.report',
			'required' => false,
			'allow_delete' => true,
			'delete_label' => 'Supprimer',
			'download_link' => false,
			'asset_helper' => true,
		]);

		$builder->add('users', EntityType::class, [
			'class'         => User::class,
			'choice_label'  => 'getFullName',
			'required'      => true,
			'multiple'      => true,
			'expanded'      => false,
			'label'         => 'entity.Project.user.user_list',
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
			}

		]);


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Meeting::class,
        ]);
        $resolver->setRequired(['project', 'users']);
    }
}
