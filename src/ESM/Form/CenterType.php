<?php

namespace App\ESM\Form;

use App\ESM\Entity\Center;
use App\ESM\Entity\DropdownList\CenterStatus;
use App\ESM\Entity\Institution;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CenterType
 * @package App\ESM\Form
 */
class CenterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('number', TextType::class, [
                'label' => 'entity.Center.field.number',
                'attr' => [
                    'placeholder' => 'entity.Center.field.number',
                ],
                'disabled' => $options['isSelected'],
            ])

            ->add('name', TextType::class, [
                'label' => 'entity.Center.field.name',
                'attr' => [
                    'placeholder' => 'entity.Center.field.name',
                ],
            ])

            ->add('institutions', EntityType::class, [
                'label' => 'entity.Center.field.institution',
                'class' => Institution::class,
                'multiple' => true,
                'required' => true,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $countryIds = array_map(function ($country) {
                        return $country->getId();
                    }, $options['project']->getCountries()->toArray());

                    return $er->createQueryBuilder('i')
                        ->innerJoin('i.services', 's')
                        ->innerJoin('i.country', 'c')
                        ->where('i.deletedAt IS NULL')
                        ->andWhere('c.id IN (:countries)')
                        ->andWhere('s.deletedAt IS NULL')
                        ->setParameter('countries', $countryIds)
                        ->orderBy('i.name', 'ASC');
                },
                'choice_label' => 'name',
            ]);

        if (!$options['isCreating']) {
            $builder->add('centerStatus', EntityType::class, [
                'label' => 'entity.Center.field.centerStatus',
                'class' => CenterStatus::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('c')
                        ->where('c.type IN (:types)')
                        ->setParameter('types', $options['isSelected'] ? [2, 3, 4] : [1, 2])
                        ->orderBy('c.position', 'ASC');
                },
                'choice_label' => 'label',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Center::class,
            'isCreating' => false,
        ]);
        $resolver->setRequired('project');
        $resolver->setRequired('isSelected');
    }
}
