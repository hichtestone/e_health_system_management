<?php

namespace App\ESM\Form;

use App\ESM\Entity\DeviationReview;
use App\ESM\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DeviationReviewType
 * @package App\ESM\Form
 */
class DeviationReviewType extends AbstractType
{
    public const ROLE_DEVIATION_REVIEW = 'ROLE_DEVIATION_REVIEW';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['deleteOrCloseReview']) {
            if (!$options['isCrex']) {
                $builder
                    ->add('type', ChoiceType::class, [
                        'label' => 'entity.Deviation.DeviationReview.field.typeReview',
                        'choices' => [
                            'Opérationelle' => DeviationReview::TYPE_OPERATIONAL,
                            'Contrôle qualité' => DeviationReview::TYPE_QUALITY_CONTROL,
                        ],
                        'required' => true,
                    ])
                    ->add('reader', EntityType::class, [
                        'label' => 'entity.Deviation.DeviationReview.field.readerName',
                        'class' => User::class,
                        'query_builder' => function (EntityRepository $er) use ($options) {
							if ($options['data']->getId()) {
								return $er->createQueryBuilder('u')
									->innerJoin('u.userProjects', 'up')
									->innerJoin('u.profile', 'p')
									->innerJoin('p.roles', 'r')
									->where('up.project = :project')
									->andWhere('r.code = :role')
									->andWhere('u.id = :currentUser')
									->orWhere('up.disabledAt IS NULL')
									->setParameter('project', $options['project'])
									->setParameter('role', DeviationReviewType::ROLE_DEVIATION_REVIEW)
									->setParameter('currentUser', $options['data']->getReader()->getId())
									->orderBy('u.lastName', 'ASC');
							} else {
								return $er->createQueryBuilder('u')
									->innerJoin('u.userProjects', 'up')
									->innerJoin('u.profile', 'p')
									->innerJoin('p.roles', 'r')
									->where('up.project = :project')
									->andWhere('up.disabledAt IS NULL')
									->andWhere('r.code = :role')
									->setParameter('project', $options['project'])
									->setParameter('role', DeviationReviewType::ROLE_DEVIATION_REVIEW)
									->orderBy('u.lastName', 'ASC');
							}
                        },
                        'choice_label' => 'getFullName',
                        'required' => true,
                    ]);
            }

            $builder->add('comment', TextareaType::class, [
                    'label' => 'entity.Deviation.DeviationReview.field.comment',
                    'attr' => [
                        'placeholder' => 'entity.Deviation.DeviationReview.field.comment',
                    ],
                    'required' => false,
                ])
                ->add('doneAt', DateTimeType::class, [
                    'label' => 'entity.Deviation.DeviationReview.field.doneAtReview',
                    'attr' => [
                        'placeholder' => 'dd/MM/yyyy',
                        'class' => 'js-datepicker',
                    ],
                    'html5' => false,
                    'widget' => 'single_text',
                    'required' => true,
                    'format' => 'dd/MM/yyyy',
                ])
                ->add('deviationIds', HiddenType::class, [
                    'mapped' => false,
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => DeviationReview::class,
            ])
            ->setRequired(['project', 'deleteOrCloseReview', 'isCrex'])
        ;
    }
}
