<?php

namespace App\ESM\Form;

use App\ESM\Entity\DropdownList\Country;
use App\ESM\Entity\DropdownList\CountryDepartment;
use App\ESM\Entity\Institution;
use App\ESM\Validator\UnchangedInstitution;
use App\ESM\Validator\UnitInstitution;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class InstitutionType
 * @package App\ESM\Form
 */
class InstitutionType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('institutionType', EntityType::class, [
                'label' => 'entity.Institution.field.type',
                'class' => \App\ESM\Entity\DropdownList\InstitutionType::class,
                'choice_label' => 'label',
            ])
            ->add('name', TextType::class, [
                'label' => 'entity.Institution.field.name',
                'attr' => [
                    'placeholder' => 'entity.Institution.field.name',
                ],
            ])
            ->add('address1', TextType::class, [
                'label' => 'entity.Institution.field.address1',
                'attr' => [
                    'placeholder' => 'entity.Institution.field.address1',
                ],
            ])
            ->add('address2', TextType::class, [
                'label' => 'entity.Institution.field.address2',
                'attr' => [
                    'placeholder' => 'entity.Institution.field.address2',
                ],
                'required' => false,
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'entity.Institution.field.postalCode',
                'attr' => [
                    'placeholder' => 'entity.Institution.field.postalCode',
                ],
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'label' => 'entity.Institution.field.city',
                'attr' => [
                    'placeholder' => 'entity.Institution.field.city',
                ],
            ])
            ->add('finess', TextType::class, [
                'label' => 'entity.Institution.field.finess',
                'attr' => [
                    'placeholder' => 'entity.Institution.field.finess',
                    'maxlength' => 9,
                    'pattern' => '\d+',
                ],
				'constraints' => [
					new UnitInstitution(),
				],
                'required' => false,
            ])
            ->add('siret', TextType::class, [
                'label' => 'entity.Institution.field.siret',
                'attr' => [
                    'placeholder' => 'entity.Institution.field.siret',
                    'maxlength' => 14,
                    'pattern' => '\d+',
                ],
				'constraints' => [
					new UnitInstitution(),
				],
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'entity.Institution.field.email',
                'attr' => [
                    'placeholder' => 'entity.Institution.field.email',
                ],
                'required' => false,
            ])
            ->add('phone', TextType::class, [
                'label' => 'entity.Institution.field.phone',
                'attr' => [
                    'placeholder' => 'entity.Institution.field.phone',
                ],
                'required' => false,
            ])
            ->add('fax', TextType::class, [
                'label' => 'entity.Institution.field.fax',
                'attr' => [
                    'placeholder' => 'entity.Institution.field.fax',
                ],
                'required' => false,
            ])
            ->add('country', EntityType::class, [
                'label' => 'entity.Institution.field.country',
                'class' => Country::class,
                'choice_label' => 'name',
				'constraints' => [
					new UnchangedInstitution(),
				],
            ])
            ->add('countryDepartment', EntityType::class, [
                'label' => 'entity.Institution.field.department',
                'class' => CountryDepartment::class,
                'choice_label' => function ($countryDep) {
                    return $countryDep->getCode() . ' - ' . $countryDep->getName();
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('countryDepartment')
                        ->innerJoin('countryDepartment.parent', 'parent')
                        ->where('parent.country = 1')
                        ->orderBy('countryDepartment.code', 'ASC');
                },
                'required' => false,
                'choice_attr' => function($choice, $key, $value) {
                    $parent_country_dept = $this->entityManager->getRepository(CountryDepartment::class)->find($value);

                    return ['data-parent' => ($parent_country_dept->getParent()->getName() ?? '')];
                },
            ])
			->add('idCurrent', HiddenType::class, [
				'mapped' => false,
				'data' => $options['data']->getId() ?? null,
			])
		;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Institution::class,
        ]);
    }
}
