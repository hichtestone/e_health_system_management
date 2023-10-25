<?php

namespace App\ESM\Form;

use App\ESM\Entity\DeviationReview;
use App\ESM\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RevueCrexType
 * @package App\ESM\Form
 */
class RevueCrexType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('doneAt', DateTimeType::class, [
                'label' => 'entity.DeviationReview.field.doneAtReview',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => true,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('validatedAt', DateTimeType::class, [
                'label' => 'entity.DeviationReview.field.doneAtReview',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => true,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'entity.DeviationReview.field.comment',
                'attr' => [
                    'placeholder' => 'entity.DeviationReview.field.comment',
                ],
                'required' => false,
            ])
            ->add('reader', EntityType::class, [
                'label' => 'entity.DeviationReview.field.readerName',
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->innerJoin('u.profile', 'p')
                        ->where("p.acronyme = 'CP'")
                        ->orWhere("p.acronyme = 'QA'")
                        ->orderBy('u.lastName', 'ASC');
                },
                'choice_label' => 'lastName',
                'required' => true,
            ])
            ->add('deviationIds', HiddenType::class, [
                'mapped' => false,
            ]);
        $builder->get('doneAt')->addModelTransformer(new CallbackTransformer(
            function ($value) {
                if (!$value) {
                    return new \DateTime('now');
                }
                return $value;
            },
            function ($value) {
                return $value;
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DeviationReview::class,
        ]);
    }
}
