<?php

namespace App\ESM\Form;

use App\ESM\Entity\Center;
use App\ESM\Entity\DocumentTracking;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DocumentTrackingCenterMassType
 * @package App\ESM\Form
 */
class DocumentTrackingCenterMassType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('documentTrackings', EntityType::class, [
                'class' => DocumentTracking::class,
                'label' => false,
                'multiple' => true,
                'expanded' => false,
                'attr' => [
                    'hidden' => true,
                    'class' => 'd-none',
                ],
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
                        ->andWhere('dt.level = :centerLevel')
                        ->setParameter('centerLevel', DocumentTracking::levelCenter)
                        ->andWhere('dt.disabledAt IS NULL')
                        ->orderBy('dt.title', 'ASC')
                        ->addOrderBy('dt.version', 'ASC');

                    return $qb;
                },
            ])
            ->add('centers', EntityType::class, [
                'label' => 'Centres',
                'class' => Center::class,
                'multiple' => true,
                'required' => true,
                'attr' => [
                    'class' => 'tail-select',
                ],
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $qb = $er->createQueryBuilder('c')
                        ->innerJoin('c.interlocutorCenters', 'ic')
                        ->innerJoin('c.centerStatus', 's')
                        ->where('ic.disabledAt IS NULL')
                        ->andWhere('c.project = :project')
                        ->andWhere("s.label != 'Clôturé'")
                        ->setParameter('project', $options['project'])
                        ->orderBy('c.number', 'ASC');
                    if (null !== $options['country']) {
                        $qb->innerJoin('c.institutions', 'i')
                            ->innerJoin('i.country', 'co')
                            ->andWhere('co.id = :country_id')
                            ->setParameter('country_id', $options['country']);
                    }

                    return $qb;
                },
                'choice_label' => function ($center) {
                    return $center->getNumber().' '.$center->getName();
                },
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
