<?php

namespace App\ESM\Form;

use App\ESM\Entity\DocumentTracking;
use App\ESM\Entity\Interlocutor;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DocumentTrackingInterlocutorMassType
 * @package App\ESM\Form
 */
class DocumentTrackingInterlocutorMassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('documentTrackings', EntityType::class, [
                'class' => DocumentTracking::class,
                'label' => false,
                'attr' => [
                    'hidden' => true,
                    'class' => 'd-none',
                ],
                'multiple' => true,
                'expanded' => false,
                'group_by' => function ($choice, $key, $value) {
                    $r = $choice->getTitle();
                    if (null !== $choice->getCountry()) {
                        $r .= ' ('.$choice->getCountry()->getName().')';
                    }

                    return $r;
                },
                'placeholder' => '<<Document>>',
                'choice_label' => function ($documentTracking) {
                    return $documentTracking->getTitle().' '.$documentTracking->getVersion();
                },
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $qb = $er->createQueryBuilder('dt')
                        ->where('dt.project = :project')
                        ->setParameter('project', $options['project'])
                        ->andWhere('dt.level = :invLevel')
                        ->setParameter('invLevel', DocumentTracking::levelInterlocutor)
                        ->andWhere('dt.disabledAt IS NULL')
                        ->orderBy('dt.title', 'ASC')
                        ->addOrderBy('dt.version', 'ASC');

                    return $qb;
                },
            ])
            ->add('interlocutors', EntityType::class, [
                'label' => 'entity.Center.field.interlocutor',
                'class' => Interlocutor::class,
                'multiple' => true,
                'required' => true,
                'attr' => [
                    'class' => 'tail-select',
                ],
                // todo groupby centre ?
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $qb = $er->createQueryBuilder('i')
                        ->innerJoin('i.interlocutorCenters', 'ic')
                        ->innerJoin('ic.center', 'c')
                        ->where('ic.disabledAt IS NULL')
                        ->andWhere('c.project = :project')
                        ->setParameter('project', $options['project'])
                        ->orderBy('i.lastName', 'ASC');
                    if (null !== $options['country']) {
                        $qb->innerJoin('ic.service', 's')
                            ->innerJoin('s.institution', 'ins')
                            ->innerJoin('ins.country', 'co')
                            ->andWhere('co.id = :country_id')
                            ->setParameter('country_id', $options['country']);
                    }

                    return $qb;
                },
                'choice_label' => 'fullName',
            ])
            ->add('sentAt', DateTimeType::class, [
                'label' => 'entity.DocumentTracking.field.sentAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => true,
                'format' => 'dd/MM/yyyy',
            ])
            /*->add('receivedAt', DateTimeType::class, [
                'label' => 'entity.DocumentTracking.field.receivedAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])*/;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
        $resolver->setRequired('project');
        $resolver->setRequired('country');
    }
}
