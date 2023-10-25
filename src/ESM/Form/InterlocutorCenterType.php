<?php

namespace App\ESM\Form;

use App\ESM\Entity\Interlocutor;
use App\ESM\Entity\InterlocutorCenter;
use App\ESM\Entity\Service;
use App\ESM\Form\BaseType\BoolType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class InterlocutorCenterType
 * @package App\ESM\Form
 */
class InterlocutorCenterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

			->add('service', EntityType::class, [
                'label' => 'Service',
                'class' => Service::class,
                'choice_label' => 'name',
                'group_by' => function ($choice, $key, $value) {
                    $institution = $choice->getInstitution();

                    return $institution->getName().' '.$institution->getCity();
                },
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $qb = $er->createQueryBuilder('s')
                        ->innerJoin('s.institution', 'ins')
                        ->innerJoin('ins.centers', 'c')
                        ->where('s.deletedAt IS NULL')
                        ->andWhere('c = :center')
                        ->setParameter('center', $options['center']);
                    if (null === $options['interlocutor']) {
                        $qb->andWhere('ins.id IN (:institutions)')
                            ->setParameter('institutions', $options['center']->getInstitutions());
                    } else {
                        $qb->innerJoin('ins.interlocutors', 'int')
                            ->andWhere('int.id in (:interlocutors)')
                            ->setParameter('interlocutors', $options['interlocutor']);
                    }

                    return $qb->orderBy('ins.name', 'ASC');
                },
                'placeholder' => '<<Service>>',
            ])

        	->add('interlocutor', EntityType::class, [
                'label' => 'entity.Interlocutor.label',
                'class' => Interlocutor::class,
                'choice_label' => 'getFullName',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('int')
                        ->innerJoin('int.institutions', 'ins')
                        ->where('ins.id IN (:institutions)')
                        ->andWhere('ins.deletedAt IS NULL')
                        ->setParameter('institutions', $options['center']->getInstitutions())
                        ->orderBy('int.lastName', 'ASC');
                },
                'placeholder' => '<<Interlocutor>>',
                'disabled' => null !== $options['interlocutor'],
            ])

			->add('enabledAt', DateTimeType::class, [
				'label' => 'entity.Interlocutor.field.participation_start',
				'attr' => [
					'placeholder' => 'dd/MM/yyyy',
					'class' => 'js-datepicker',
				],
				'html5' 	=> false,
				'widget' 	=> 'single_text',
				'required'  => true,
				'format' 	=> 'dd/MM/yyyy',
			])

			->add('disabledAt', DateTimeType::class, [
				'label' => 'entity.Interlocutor.field.participation_end',
				'attr' => [
					'placeholder' => 'dd/MM/yyyy',
					'class' => 'js-datepicker',
				],
				'html5' 	=> false,
				'widget' 	=> 'single_text',
				'required'  => false,
				'format' 	=> 'dd/MM/yyyy',
			]);

//		if (null !== $options['data']->getId() && !$options['isPI']) {
//
//			$builder->add('disabledAt', DateTimeType::class, [
//				'label' => 'Date de fin de participation',
//				'attr' => [
//					'placeholder' => 'dd/MM/yyyy',
//					'class' => 'js-datepicker',
//				],
//				'html5' => false,
//				'widget' => 'single_text',
//				'required' => false,
//				'format' => 'dd/MM/yyyy',
//			]);
//		}

		$builder->add('isPrincipalInvestigator', BoolType::class, [
			'label' => 'Investigator principal',
			'required' => false,
		]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InterlocutorCenter::class,
            'interlocutor' => null,
			'isPI' => false,
        ]);
        $resolver->setRequired('center');
    }
}
