<?php

namespace App\ESM\Form;

use App\ESM\Entity\DocumentTransverse;
use App\ESM\Form\EventSubscriber\DocumentTypeSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DocumentTransverseType
 * @package App\ESM\Form
 */
class DocumentTransverseType extends AbstractType
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $request = $this->requestStack->getCurrentRequest();
        $route = $request->attributes->get('_route');

        // Other fields
        $builder->addEventSubscriber(new DocumentTypeSubscriber($route));

        $builder
            ->add('name', TextType::class, [
                'label' => 'entity.DocumentTransverse.field.name',
                'attr' => [
                    'placeholder' => 'entity.DocumentTransverse.field.name',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DocumentTransverse::class,
        ]);
    }
}
