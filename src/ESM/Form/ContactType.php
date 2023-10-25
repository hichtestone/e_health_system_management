<?php

namespace App\ESM\Form;

use App\ESM\Entity\Contact;
use App\ESM\Entity\DropdownList\ContactObject;
use App\ESM\Entity\DropdownList\ContactPhase;
use App\ESM\Entity\DropdownList\ContactTypeRecipient;
use App\ESM\Entity\Interlocutor;
use App\ESM\Entity\User;
use App\ESM\Form\BaseType\BoolType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ContactType
 * @package App\ESM\Form
 */
class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->interlocutorIntervenantFields($builder, $options);
        $this->otherFields($builder);
    }

    public function interlocutorIntervenantFields(FormBuilderInterface $builder, array $options)
    {
        $recipientOptions = [
            'class' => User::class,
            'choice_label' => 'getFullName',
            'required' => true,
            'label' => 'entity.Contact.field.transmitter',
            'query_builder' => function (EntityRepository $er) use ($options) {
				if ($options['data']->getId()) {
					return $er->createQueryBuilder('u')
						->innerJoin('u.userProjects', 'up')
						->where('up.project = :project')
						->andWhere('u.id = :currentUser')
						->orWhere('up.disabledAt IS NULL')
						->setParameter('currentUser', $options['data']->getIntervenant()->getId())
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
        ];
        if (null !== $options['user']) {
            $recipientOptions['data'] = $options['user'];
        }

        $builder
            ->add('typeRecipient', EntityType::class, [
                'label' => 'entity.Contact.field.type_recipient',
                'class' => ContactTypeRecipient::class,
                'choice_label' => 'label',
                'expanded' => true,
                'required' => true,
                'placeholder' => false,
                'disabled' => null !== $options['from'] || $options['edit'],
            ])
            ->add('intervenant', EntityType::class, $recipientOptions)
            ->add('interlocutors', EntityType::class, [
                'class' => Interlocutor::class,
                'choice_label' => 'getFullName',
                'required' => true,
                'multiple' => true,
                'expanded' => false,
                'label' => 'entity.Contact.field.interlocutors',
                'query_builder' => function (EntityRepository $er) use ($options) {
					if ($options['data']->getId()) {
						return $er->createQueryBuilder('interlocutor')
							->innerJoin('interlocutor.interlocutorCenters', 'interlocutorCenter')
							->innerJoin('interlocutorCenter.center', 'center')
							->innerJoin('center.project', 'project')
							->where('project.id = :idProject')
							->andWhere('center.deletedAt IS NULL')
							->andWhere('interlocutor.deletedAt IS NULL')
							->andWhere('interlocutorCenter.disabledAt IS NULL')
							->orWhere('interlocutor.id in (:interlocutors)')
							->setParameter('interlocutors', $options['interlocutors'])
							->setParameter('idProject', $options['project']->getId())
							->orderBy('interlocutor.lastName', 'ASC');
					} else {
						return $er->createQueryBuilder('interlocutor')
							->innerJoin('interlocutor.interlocutorCenters', 'interlocutorCenter')
							->innerJoin('interlocutorCenter.center', 'center')
							->innerJoin('center.project', 'project')
							->where('project.id = :idProject')
							->andWhere('center.deletedAt IS NULL')
							->andWhere('interlocutor.deletedAt IS NULL')
							->andWhere('interlocutorCenter.disabledAt IS NULL')
							->setParameter('idProject', $options['project']->getId())
							->orderBy('interlocutor.lastName', 'ASC');
					}
                },
            ])
            ->add('intervenants', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'getFullName',
                'required' => true,
                'multiple' => true,
                'expanded' => false,
                'label' => 'entity.Contact.field.intervenants',
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

    public function otherFields(FormBuilderInterface $builder)
    {
        $builder
            ->add('phase', EntityType::class, [
                'label' => 'entity.Contact.field.phase',
                'class' => ContactPhase::class,
                'choice_label' => 'label',
                'expanded' => true,
                'required' => true,
                'placeholder' => false,
            ])
            ->add('type', EntityType::class, [
                'label' => 'entity.Contact.field.type',
                'class' => \App\ESM\Entity\DropdownList\ContactType::class,
                'choice_label' => 'label',
                'required' => true,
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'entity.Contact.field.date',
                'attr' => [
                    'placeholder' => 'entity.Contact.field.date',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => true,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('hour', TextType::class, [
                'label' => 'entity.Contact.field.hour',
                'attr' => [
                    'placeholder' => 'entity.Contact.field.hour',
                    'class' => 'js-datepicker-time',
                ],
            ])
            ->add('cc', TextType::class, [
                'label' => 'entity.Contact.field.cc',
                'attr' => [
                    'placeholder' => 'entity.Contact.field.cc',
                ],
                'required' => false,
            ])
            ->add('object', EntityType::class, [
                'label' => 'entity.Contact.field.object',
                'class' => ContactObject::class,
                'choice_label' => 'label',
                'required' => true,
            ])
			->add('objectReason', TextType::class, [
				'label' => 'entity.Contact.field.reason',
				'required' => false,
			])
            ->add('completed', BoolType::class, [
                'label' => 'entity.Contact.field.completed',
            ])
            ->add('detail', TextType::class, [
                'label' => 'entity.Contact.field.detail',
                'attr' => [
                    'placeholder' => 'entity.Contact.field.detail',
                ],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => Contact::class,
                'user' => null,
                'from' => null,
                'edit' => false,
            ])
            ->setRequired(['project', 'users', 'interlocutors']);
    }
}
