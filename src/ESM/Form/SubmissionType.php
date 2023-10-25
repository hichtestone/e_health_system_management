<?php

namespace App\ESM\Form;

use App\ESM\Entity\DropdownList\Country;
use App\ESM\Entity\DropdownList\NameSubmissionRegulatory;
use App\ESM\Entity\DropdownList\TypeDeclaration;
use App\ESM\Entity\DropdownList\TypeSubmission;
use App\ESM\Entity\DropdownList\TypeSubmissionRegulatory;
use App\ESM\Entity\Submission;
use App\ESM\Form\BaseType\BoolType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SubmissionType
 * @package App\ESM\Form
 */
class SubmissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', EntityType::class, [
                'label' => 'entity.Project.register.labels.countries',
                'class' => Country::class,
                'required' => false,
                'placeholder' => 'entity.Project.register.labels.countries',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('c')
                        ->innerJoin('c.projects', 'p')
                        ->where('p.id = :id')
                        ->setParameter('id', $options['project']->getId())
                        ->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name',
            ])
        ;

        $builder->get('country')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $this->addTypeSubmissionRegulatoryField($form->getParent(), $form->getData());
            }
        );
        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();
                /* @var $name NameSubmissionRegulatory */
                $name = $data->getNameSubmissionRegulatory();
                $form = $event->getForm();
                if ($name) {
                    // On récupère le type et le pays
                    $type = $name->getTypeSubmissionRegulatory();
                    $country = $type->getCountry();
                    // On crée les 2 champs supplémentaires
                    $this->addTypeSubmissionRegulatoryField($event->getForm(), $country);
                    $this->addNameSubmissionRegulatoryField($event->getForm(), $type);

                    // On set les données
                    $form->get('country')->setData($country);
                    $form->get('typeSubmissionRegulatory')->setData($type);
                } else {
                    // On crée les 2 champs en les laissant vide (champs utilisé pour le JavaScript)
                    $this->addTypeSubmissionRegulatoryField($form, null);
                    $this->addNameSubmissionRegulatoryField($form, null);
                }
            }
        );

        $builder
            ->add('typeSubmission', EntityType::class, [
                'label' => 'entity.Submission.field.typeSubmission',
                'class' => TypeSubmission::class,
                'required' => false,
                'placeholder' => 'entity.Submission.field.typeSubmission',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.label', 'ASC');
                },
                'choice_label' => 'label',
            ])
            ->add('typeDeclaration', EntityType::class, [
                'label' => 'entity.Submission.field.typeDeclaration',
                'class' => TypeDeclaration::class,
                'required' => false,
                'placeholder' => 'entity.Submission.field.typeDeclaration',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.label', 'ASC');
                },
                'choice_label' => 'label',
            ])
            ->add('amendmentNumber', TextareaType::class, [
                'label' => 'entity.Submission.field.amendment_number',
                'attr' => [
                    'placeholder' => 'entity.Submission.field.amendment_number',
                ],
                'empty_data' => '',
                'required' => false,
            ])
            ->add('submissionAt', DateTimeType::class, [
                'label' => 'entity.Submission.field.submissionAt',
                'attr' => [
                    'placeholder' => 'dd/mm/yyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => true,
                'format' => 'dd/MM/yyyy',
                'empty_data' => null,
            ])
            ->add('estimatedSubmissionAt', DateTimeType::class, [
                'label' => 'entity.Submission.field.estimatedSubmissionAt',
                'attr' => [
                    'placeholder' => 'dd/mm/yyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => true,
                'format' => 'dd/MM/yyyy',
                'empty_data' => null,
            ])
            ->add('question', BoolType::class, [
                'label' => 'entity.Submission.field.question',
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'entity.Submission.field.comment',
                'attr' => [
                    'placeholder' => 'entity.Submission.field.comment',
                ],
                'empty_data' => '',
                'required' => false,
            ])
            ->add('fileNumber', TextType::class, [
                'label' => 'entity.Submission.field.fileNumber',
                'attr' => [
                    'placeholder' => 'entity.Submission.field.fileNumber',
                ],
                'empty_data' => '',
            ])
            ->add('admissibilityAt', DateTimeType::class, [
                'label' => 'entity.Submission.field.admissibilityAt',
                'attr' => [
                    'placeholder' => 'dd/mm/yyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => true,
                'format' => 'dd/MM/yyyy',
                'empty_data' => null,
            ])
            ->add('authorizationAt', DateTimeType::class, [
                'label' => 'entity.Submission.field.authorizationAt',
                'attr' => [
                    'placeholder' => 'dd/mm/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
        ;
    }

    /**
     * Rajouter un champ type d'autorité.
     */
    public function addTypeSubmissionRegulatoryField(FormInterface $form, ?Country $country): void
    {
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
            'typeSubmissionRegulatory',
            EntityType::class,
            null,
            [
                'label' => 'entity.Submission.field.authorityType',
                'class' => TypeSubmissionRegulatory::class,
                'placeholder' => $country ? 'entity.Submission.field.authorityType' : 'entity.Project.register.labels.countries',
                'required' => false,
                'auto_initialize' => false,
                'choices' => $country ? $country->getTypeSubmissionRegulatories() : [],
            ]
        );
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $this->addNameSubmissionRegulatoryField($form->getParent(), $form->getData());
            }
        );

        $form->add($builder->getForm());
    }

    /**
     * Rajouter un champ type d'autorité.
     */
    public function addNameSubmissionRegulatoryField(FormInterface $form, ?TypeSubmissionRegulatory $typeSubmissionRegulatory)
    {
        $form->add('nameSubmissionRegulatory', EntityType::class, [
            'label' => 'entity.Submission.field.authorityName',
            'class' => NameSubmissionRegulatory::class,
            'placeholder' => $typeSubmissionRegulatory ? 'entity.Submission.field.authorityName' : 'entity.Submission.field.authorityType',
            'choices' => $typeSubmissionRegulatory ? $typeSubmissionRegulatory->getNameSubmissionRegulatories() : [],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Submission::class,
        ]);
        $resolver->setRequired('project');
    }
}
