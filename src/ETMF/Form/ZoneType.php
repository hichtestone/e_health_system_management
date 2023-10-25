<?php

namespace App\ETMF\Form;

use App\ETMF\Entity\Zone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ZoneType
 * @package App\ETMF\Form
 */
class ZoneType extends AbstractType
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
				'label' => 'etmf.zone.field.name',
				'attr' => [
					'placeholder' => 'etmf.zone.field.name',
				],
                'required' => true,
			])

            ->add('code',TextType::class, [
				'label' => 'etmf.zone.field.number',
				'attr' => [
					'placeholder' => 'etmf.zone.field.number',
					'pattern' => '\d+',
				],
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

		$isZoneNameExist = false;
		$isZoneCodeExist = false;

		if (count($changeSet) > 0) {

			if (array_key_exists('name', $changeSet)) {
				$isZoneNameExist = (bool) $this->em->getRepository(Zone::class)->findBy(['name' => $data->getName(), 'deletedAt' => null]);
			}

			if (array_key_exists('code', $changeSet)) {
				$isZoneCodeExist = (bool) $this->em->getRepository(Zone::class)->findBy(['code' => $data->getCode(), 'deletedAt' => null]);
			}

		} else {

			$isZoneNameExist = (bool) $this->em->getRepository(Zone::class)->findBy(['name' => $data->getName(), 'deletedAt' => null]);
			$isZoneCodeExist = (bool) $this->em->getRepository(Zone::class)->findBy(['code' => $data->getCode(), 'deletedAt' => null]);
		}

		if ($isZoneNameExist) {
			$form->get('name')->addError(new FormError('Une zone avec ce nom existe déjà'));
		}

		if ($isZoneCodeExist) {
			$form->get('code')->addError(new FormError('Une zone avec ce numéro existe déjà'));
		}
	}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Zone::class,
        ]);
    }
}
