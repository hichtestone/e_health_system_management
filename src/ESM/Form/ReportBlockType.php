<?php

namespace App\ESM\Form;

use App\ESM\Entity\ReportBlock;
use App\ESM\Entity\ReportModelVersion;
use App\ESM\Validator\UnitReportBlock;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ReportBlockType
 * @package App\ESM\Form
 */
class ReportBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'entity.report_block.field.name',
                'attr' => [
                    'placeholder' => 'entity.report_block.field.name',
                ],
                'required' => true,
                'constraints' => [
                    new UnitReportBlock(),
                ],
            ])
           ->add('reportModelVersion', EntityType::class, [
                'label' => false,
                'class' => ReportModelVersion::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('reportModelVersion')
                        ->where('reportModelVersion.id = :id')
                        ->setParameter('id', $options['reportModelVersion']->getId());
                },
                'choice_label' => 'id',
               'mapped' => false,
                'attr' => [
                    'hidden' => true,
                    'class' => 'd-none',
                ],
            ])
            ->add('idCurrent', HiddenType::class, [
                'mapped' => false,
                'data' => $options['data']->getId() ?? null,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => ReportBlock::class])
            ->setRequired(['reportModelVersion'])
        ;
    }
}
