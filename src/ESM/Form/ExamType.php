<?php

namespace App\ESM\Form;

use App\ESM\Entity\Exam;
use App\ESM\Entity\Project;
use App\ESM\Validator\UnitVariableExam;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ExamType
 * @package App\ESM\Form
 */
class ExamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ordre', TextType::class, [
                'label' => 'entity.ExamSetting.field.ordre',
                'attr' => [
                    'placeholder' => 'entity.ExamSetting.field.ordre',
                    'pattern' => '\d+',
                ],
                'required' => true,
            ])
            ->add('type', EntityType::class, [
                'label' => 'entity.ExamSetting.field.type',
                'class' => \App\ESM\Entity\DropdownList\ExamType::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.label', 'ASC');
                },
				'placeholder' => '<<Type d\'examen>>',
                'choice_label' => 'label',
                'required' => true,
            ])
			->add('typeReason', TextType::class, [
				'label' => 'Préciser',
				'required' => false,
			])
            ->add('name', TextType::class, [
                'label' => 'entity.ExamSetting.field.name',
                'attr' => [
                    'placeholder' => 'entity.ExamSetting.field.name',
                ],
                'required' => true,
                'constraints' => [
                    new UnitVariableExam(),
                ],
            ])
            ->add('price', NumberType::class, [
                'label' => 'entity.ExamSetting.field.price',
                'attr' => [
                    'placeholder' => 'entity.ExamSetting.field.price',
                ],
                'required' => false,
            ])
           ->add('project', HiddenType::class, [
				'mapped' => false,
				'data' => $options['data']->getProject()->getId() ?? null,
			]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Exam::class,
        ]);

        $resolver->setRequired('project');
    }
}
