<?php

namespace App\ETMF\Form;

use App\ESM\Entity\Center;
use App\ESM\Entity\DropdownList\Country;
use App\ESM\Entity\DropdownList\Sponsor;
use App\ESM\Entity\Project;
use App\ETMF\Entity\Artefact;
use App\ETMF\Entity\DocumentVersion;
use App\ETMF\Entity\Section;
use App\ETMF\Entity\Tag;
use App\ETMF\Entity\Zone;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SearchType.
 */
class SearchType extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sponsor', ChoiceType::class, [
                'label' => 'Promoteur',
                'choices' => array_flip($this->em->getRepository(Sponsor::class)->getAllID()),
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ])
            ->add('project', ChoiceType::class, [
                'label' => 'Étude',
                'choices' => array_flip($this->em->getRepository(Project::class)->getAllID()),
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ])
            ->add('zone', ChoiceType::class, [
                'label' => 'Zone',
                'choices' => array_flip($this->em->getRepository(Zone::class)->getAllID()),
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ])
            ->add('section', ChoiceType::class, [
                'label' => 'Section',
                'choices' => array_flip($this->em->getRepository(Section::class)->getAllID()),
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ])
            ->add('artefact', ChoiceType::class, [
                'label' => 'Artefact',
                'choices' => array_flip($this->em->getRepository(Artefact::class)->getAllID()),
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ])
            ->add('country', ChoiceType::class, [
                'label' => 'Country',
                'choices' => array_flip($this->em->getRepository(Country::class)->getAllID()),
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ])
            ->add('center', ChoiceType::class, [
                'label' => 'Centre',
                'choices' => array_flip($this->em->getRepository(Center::class)->getAllID()),
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ])
            ->add('tag', ChoiceType::class, [
                'label' => 'Tag',
                'choices' => array_flip($this->em->getRepository(Tag::class)->getAllID()),
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ])
            ->add('tag', ChoiceType::class, [
                'label' => 'Tag',
                'choices' => array_flip($this->em->getRepository(Tag::class)->getAllID()),
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => array_flip(DocumentVersion::STATUS),
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ]);
            /*->add('submit', SubmitType::class, [
                'label' => 'Rechercher'
            ]);;*/
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
