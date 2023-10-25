<?php

namespace App\ETMF\Form;

use App\ETMF\Entity\Section;
use App\ETMF\Entity\Zone;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SectionType
 * @package App\ETMF\Form
 */
class SectionType extends AbstractType
{
	private $em;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->em = $entityManager;
	}

	public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class, [
				'label' => 'etmf.section.field.name',
				'attr' => [
					'placeholder' => 'etmf.section.field.name',
				],
				'required' => true,
			])
            ->add('code',TextType::class, [
				'label' => 'etmf.section.field.number',
				'attr' => [
					'placeholder' => 'etmf.section.field.number',
					'pattern' => '\d+',
				],
				'required' => true,
			])
			->add('zone', EntityType::class, [
				'label' => 'etmf.section.field.zone',
				'class' => Zone::class,
				'query_builder' => function (EntityRepository $er) use ($options) {
					return $er->createQueryBuilder('zone')
						->where('zone.deletedAt IS NULL')
						->orderBy('zone.name', 'ASC');
				},
				'choice_label' => 'name',
				'required' => true,
			]);

		$builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'onPostSubmit']);
	}

	public function onPostSubmit(FormEvent $event): void
	{
		$form = $event->getForm();
		$data = $form->getData();

		$uow = $this->em->getUnitOfWork();
		$uow->computeChangeSets();
		$changeSet = $uow->getEntityChangeSet($data);

		$isSectionNameExist = false;
		$isSectionCodeExist = false;

		if (count($changeSet) > 0) {

			if (array_key_exists('name', $changeSet)) {
				$isSectionNameExist = (bool) $this->em->getRepository(Section::class)->findBy(['zone' => $data->getZone()->getId(), 'name' => $data->getName(), 'deletedAt' => null]);
			}

			if (array_key_exists('code', $changeSet)) {
				$isSectionCodeExist = (bool) $this->em->getRepository(Section::class)->findBy(['zone' => $data->getZone()->getId(), 'code' => $data->getCode(), 'deletedAt' => null]);
			}

		} else {

			$isSectionNameExist = (bool) $this->em->getRepository(Section::class)->findBy(['zone' => $data->getZone()->getId(), 'name' => $data->getName(), 'deletedAt' => null]);
			$isSectionCodeExist = (bool) $this->em->getRepository(Section::class)->findBy(['zone' => $data->getZone()->getId(), 'code' => $data->getCode(), 'deletedAt' => null]);
		}

		if ($isSectionNameExist) {
			$form->get('name')->addError(new FormError('Une section avec ce nom est déjà présente dans cette zone'));
		}

		if ($isSectionCodeExist) {
			$form->get('code')->addError(new FormError('Une section avec ce numéro est déjà présente dans cette zone'));
		}
	}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Section::class,
        ]);
    }
}
