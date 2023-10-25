<?php

namespace App\ESM\Form;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RechercheType
 * @package App\ESM\Form
 */
class RechercheType extends AbstractType
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', ChoiceType::class,
        [
            'choices' => [
                 'entity.DocumentTransverse.search.myProjects' => 1,
            ],
            'label' => ' ',
            'required' => false,
            'placeholder' => 'entity.DocumentTransverse.search.global',
        ])
          ->setMethod('GET');

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // enable/disable CSRF protection for this form
            'csrf_protection' => false,
        ]);
    }
}
