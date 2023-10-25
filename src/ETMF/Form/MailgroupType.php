<?php

namespace App\ETMF\Form;

use App\ESM\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\ETMF\Entity\Mailgroup;

/**
 * Class MailgroupType
 * @package App\ETMF\Form
 */
class MailgroupType extends AbstractType
{
	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('name',TextType::class, [
				'label' => 'Nom',
				'attr' => [
					'placeholder' => 'nom du groupe',
				],
				'required' => true,
			])

			->add('users', EntityType::class, [
				'label' => 'Utilisateurs',
				'class' => User::class,
				'expanded' => true,
				'multiple' => true,
				'choice_label' => function ($user) {
					return $user->getFirstname() . ' ' . $user->getLastname() . ' (' . $user->getJob()->getLabel() . ')';
				},
				'required' => true
			])

			->add('submit', SubmitType::class, [
				'label' => 'Enregistrer'
			])
		;

		$builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'onPostSubmit']);
	}

	public function onPostSubmit(FormEvent $event): void
	{
		$data = $event->getData();
		$form = $event->getForm();

		if ($data->getUsers()->isEmpty()) {
			$form->get('users')->addError(new FormError('Au moins 1 utilisateur est necessaire pour la liste de diffusion !'));
		}
	}

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Mailgroup::class,
		]);
	}

	/**
	 * @return array
	 */
	private function getUsers(): array
	{
		$users = $this->entityManager->getRepository(User::class)->findAll();

		$usersData = [];
		foreach ($users as $user) {
			$usersData[$user->getId()] = $user->getFirstName() . ' ' . $user->getLastName() . ' (' . $user->getDepartment()->getLabel() . ' - ' . $user->getJob()->getLabel() . ')';
		}

		return array_flip($usersData);
	}
}
