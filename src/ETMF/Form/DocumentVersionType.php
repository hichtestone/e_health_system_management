<?php

namespace App\ETMF\Form;

use App\ESM\Entity\Center;
use App\ESM\Entity\DropdownList\Country;
use App\ESM\Entity\DropdownList\Sponsor;
use App\ESM\Entity\Project;
use App\ESM\Entity\User;
use App\ETMF\Entity\Artefact;
use App\ETMF\Entity\DocumentVersion;
use App\ETMF\Entity\DropdownList\DocumentLevel;
use App\ETMF\Entity\Section;
use App\ETMF\Entity\Tag;
use App\ETMF\Entity\Zone;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\UX\Dropzone\Form\DropzoneType;

/**
 * Class DocumentType
 * @package App\ETMF\Form
 */
class DocumentVersionType extends AbstractType
{
	/**
	 * @var TokenStorageInterface
	 */
	private $token;

    /**
     * DocumentVersionType constructor.
     * @param TokenStorageInterface $token
     */
	public function __construct(TokenStorageInterface $token)
	{
		$this->token = $token;
	}

	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
        $user = $this->token->getToken()->getUser();

		$builder
			->add('description', TextType::class, [
				'label'    => 'etmf.document.field.description',
				'attr'     => [
					'placeholder' => 'etmf.document.field.description',
				],
				'required' => true,
                'mapped'   => false,
			])

            ->add('sponsor', EntityType::class, [
                'label'         => 'Promoteur',
                'placeholder'   => 'Promoteur',
                'class'         => Sponsor::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('sponsor')
                        ->orderBy('sponsor.label', 'ASC');
                },
                'choice_label'  => 'label',
                'required'      => true,
                'mapped' => false,
		    ]);
            $builder->get('sponsor')->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) use ($user) {
                    $form = $event->getForm();
                    $this->addStudyField($form->getParent(), $form->getData(), $user);
                }
            );

		$builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) use ($user) {
                $form = $event->getForm();
                $data = $event->getData();
                $sponsor = null !== $data->getDocument() ? $data->getDocument()->getSponsor() : null;
                if (!is_null($sponsor)) {
                    $this->addStudyField($form, $sponsor, $user);
                    $form->get('sponsor')->setData($sponsor);
                } else {
                    // On crée le champs projet en le laissant vide (champs utilisé pour le JavaScript)
                    $this->addStudyField($form, null, $user);
                    //$this->addAuthorField($form, null);
                   // $this->addAuthorQaField($form, null);
                    $this->addCountryField($form, null);
                    $this->addCenterField($form, null);
                }
            }
        );
            /*->add('project', EntityType::class, [
                'label'         => 'Projet',
                'placeholder'   => 'Projet',
                'class'         => Project::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('project')
                        ->orderBy('project.name', 'ASC');
                },
                'choice_label' => 'name',
                'required'      => true,
                'mapped' => false,
		    ]);*/
        $builder->add('zone', EntityType::class, [
				'label'         => 'etmf.document.field.zone',
				'placeholder'   => '<< Sélectionnez une zone >>',
				'class'         => Zone::class,
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder('zone')
						->orderBy('zone.name', 'ASC');
				},
				'choice_label'  => 'name',
				'required'      => true,
                'mapped' => false,
			]);
            $builder->get('zone')->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) {
                    $form = $event->getForm();
                    $this->addSectionField($form->getParent(), $form->getData());
                }
            );
		$builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();
                $zone = null !== $data->getDocument() ? $data->getDocument()->getZone() : null;
                if ($zone) {
                    $form->get('zone')->setData($zone);
                    $section = $zone->getSection();
                    if ($section) {
                        $this->addArtefactField($form, $section);
                        $form->get('section')->setData($section);
                        $artefact = $section->getArtefact();
                        if ($artefact) {
                            $this->addLevelField($form, $artefact);
                            $form->get('artefact')->setData($artefact);
                        }
                    }
                } else {
                    // On crée les 2 champs en les laissant vide (champs utilisé pour le JavaScript)
                    $this->addSectionField($form, null);
                    $this->addArtefactField($form, null);
                    $this->addLevelField($form, null);
                }
            }
        );

        /*$builder->add('section', EntityType::class, [
                'label'         => 'etmf.document.field.section',
                'placeholder'   => '<< Sélectionnez une section >>',
                'class'         => Section::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('section')
                        ->orderBy('section.name', 'ASC');
                },
                'choice_label'  => 'name',
                'required'      => true,
                'mapped' => false,
            ])
            ->add('artefact', EntityType::class, [
                'label'         => 'etmf.document.field.artefact',
                'placeholder'   => 'Sélectionnez un artefact',
                'class'         => Artefact::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('artefact')
                        ->orderBy('artefact.name', 'ASC');
                },
                'choice_label'  => 'name',
                'required'      => true,
                'mapped' => false,
            ])*/
        $builder->add('documentLevels', EntityType::class, [
                'label'         => 'Sélectionnez un niveau',
                'placeholder'   => '<< Sélectionnez un niveau >>',
                'class'         => DocumentLevel::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('documentLevels')
                        ->orderBy('documentLevels.level', 'ASC');
                },
                'choice_label'  => 'level',
                'required'      => true,
                'mapped' => false,
            ])
            ->add('countries', EntityType::class, [
                'label'         => 'Pays',
                'placeholder'   => '<< Sélectionnez un pays >>',
                'class'         => Country::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('country')
                        ->orderBy('country.name', 'ASC');
                },
                'choice_label'  => 'name',
                'required'      => true,
                'multiple' => true,
                'expanded' => false,
                'mapped' => false,
            ])

            ->add('centers', EntityType::class, [
                'label'         => 'Centres',
                'placeholder'   => '<< Sélectionnez un centre >>',
                'class'         => Center::class,
                'query_builder'   => function (EntityRepository $er) {
                    return $er->createQueryBuilder('center')
                        ->innerJoin('center.centerStatus', 'status')
                        ->where('status.type IN (2, 3)')
                        ->orderBy('center.name', 'ASC');

                },
                'choice_label'  => 'name',
                'required'      => true,
                'multiple' => true,
                'expanded' => false,
                'mapped' => false,
            ])

            ->add('file', DropzoneType::class, [
                'label' => 'Document(s)',
                'required' => true,
                'multiple' => true,
            ])

            ->add('author', EntityType::class, [
                'class' => User::class,
                'label' => 'Auteur',
                'placeholder'   => '<< Auteur >>',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('user')
                        ->orderBy('user.lastName', 'ASC');
                },
                'choice_label' => 'getFullName',
                'required' => false,
            ])

            ->add('applicationAt', DateTimeType::class, [
                'label' => 'Date de mise en application',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' 	=> false,
                'widget' 	=> 'single_text',
                'required'  => false,
                'format' 	=> 'dd/MM/yyyy',
            ])
            ->add('expiredAt', DateTimeType::class, [
                'label' => 'Date d\'expiration',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' 	=> false,
                'widget' 	=> 'single_text',
                'required'  => false,
                'format' 	=> 'dd/MM/yyyy',
            ])
            ->add('signedAt', DateTimeType::class, [
                'label' => 'Date de signature du document',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' 	=> false,
                'widget' 	=> 'single_text',
                'required'  => false,
                'format' 	=> 'dd/MM/yyyy',
            ])
            ->add('validatedQaAt', DateTimeType::class, [
                'label' => 'Date AQ',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' 	=> false,
                'widget' 	=> 'single_text',
                'required'  => false,
                'format' 	=> 'dd/MM/yyyy',
         ])

        ->add('tags', EntityType::class, [
            'class' => Tag::class,
            'label' => 'Tags',
            'query_builder' => function (EntityRepository $er) use ($options) {
                return $er->createQueryBuilder('tag')
                    ->orderBy('tag.name', 'ASC');
            },
            'choice_label' => 'name',
            'multiple' => true,
            'required' => false,
        ])

        ->add('validatedQaBy', EntityType::class, [
            'class' => User::class,
            'label' => 'Sélectionnez un AQ réalisé par',
            'placeholder'   => '<< AQ Réalisé par >>',
            'query_builder' => function (EntityRepository $er) use ($options) {
                return $er->createQueryBuilder('user')
                    ->orderBy('user.lastName', 'ASC');
            },
            'choice_label' => 'getFullName',
            'required' => false,
        ]);

	}

    public function addStudyField(FormInterface $form, $sponsor): void
    {
        $projects = [];

        $sponsorProjects = !is_null($sponsor) ? $sponsor->getProjects()->toArray() : [];

        if (!is_null($sponsorProjects)) {
            foreach ($sponsorProjects as $sponsorProject) {
                $projects[] = $sponsorProject->getId();
            }
        }

        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
            'project',
            EntityType::class,
            null,
            [
                'class'           => Project::class,
                'label'           => 'Projet',
                'placeholder'     => null === $sponsor ? '<< Sélectionnez un promoteur >>' : '<< Sélectionnez une étude >>',
                'query_builder'   => function (EntityRepository $er) use ($projects) {
                    return $er->createQueryBuilder('project')
                        ->andWhere('project.id IN (:projects)')
                        ->setParameter('projects', $projects)
                        ->orderBy('project.acronyme', 'ASC');

                },
                'choice_label' => 'acronyme',
                'required'        => true,
                'auto_initialize' => false,
                'mapped' => false,
            ],
        );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($form) {
                $form = $event->getForm();
                $this->addCountryField($form->getParent(), $form->getData());
                $this->addCenterField($form->getParent(), $form->getData());
                //$this->addAuthorField($form->getParent(), $form->getData());
                //$this->addAuthorQaField($form->getParent(), $form->getData());
            }
        );

        $form->add($builder->getForm());
    }


	public function addSectionField(FormInterface $form, $zone): void
	{
		$builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
			'section',
			EntityType::class,
			null,
			[
				'class'           => Section::class,
				'label'           => 'Section',
				'placeholder'     => null === $zone ? '<< Sélectionnez une zone >>' : '<< Sélectionnez une section >>',
				'query_builder'   => function (EntityRepository $er) use ($form, $zone) {
					return $er->createQueryBuilder('section')
						->innerJoin('section.zone', 'zone')
						->where('section.deletedAt IS NULL')
						->andWhere('zone.id = :zoneID')
						->setParameter('zoneID', $zone)
						->orderBy('section.name', 'ASC');
				},
				'choice_label'    => 'name',
				'required'        => true,
				'auto_initialize' => false,
                'mapped' => false,
			],
		);

		$builder->addEventListener(
			FormEvents::POST_SUBMIT,
			function (FormEvent $event) {
				$form = $event->getForm();
				$this->addArtefactField($form->getParent(), $form->getData());
			}
		);

		$form->add($builder->getForm());
	}

	public function addArtefactField(FormInterface $form, $section): void
	{
		$builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
			'artefact',
			EntityType::class,
			null,
			[
				'class'           => Artefact::class,
				'label'           => 'Artefact',
				'placeholder'     => null === $section ? '<< Sélectionnez une section >>' : '<< Sélectionnez un artefact >>',
				'query_builder'   => function (EntityRepository $er) use ($form, $section) {
					return $er->createQueryBuilder('artefact')
						->innerJoin('artefact.section', 'section')
						->where('section.deletedAt IS NULL')
						->andWhere('section.id = :sectionID')
						->setParameter('sectionID', $section)
						->orderBy('section.name', 'ASC');
				},
				'choice_label'    => 'name',
				'required'        => true,
				'auto_initialize' => false,
                'mapped' => false,
			],
		);

       $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $levels = [];

                $artefactLevels = !is_null($form->getData()) ? $form->getData()->getArtefactLevels()->toArray() : [];

                if (!is_null($artefactLevels)) {
                    foreach ($artefactLevels as $artefactLevel) {
                        $levels[] = $artefactLevel->getId();
                    }
                }

                $this->addLevelField($form->getParent(), $form->getData());
            }
        );

		$form->add($builder->getForm());
	}

    public function addLevelField(FormInterface $form, $artefact): void
	{
        $levels = [];

        $artefactLevels = !is_null($artefact) ? $artefact->getArtefactLevels()->toArray() : [];

        if (!is_null($artefactLevels)) {
            foreach ($artefactLevels as $artefactLevel) {
                $levels[] = $artefactLevel->getId();
            }
        }


        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
            'documentLevels',
            EntityType::class,
            null,
            [
                'class'           => DocumentLevel::class,
                'label'           => 'Niveau supplémentaire',
                'placeholder'     => null === $artefact ? '<< Sélectionnez un artefact >>' : '<< Sélectionnez un niveau >>',
                'query_builder'   => function (EntityRepository $er) use ($levels) {
                    return $er->createQueryBuilder('artefactLevel')
                        ->andWhere('artefactLevel.id IN (:levels)')
                        ->setParameter('levels', $levels)
                        ->orderBy('artefactLevel.level', 'ASC');

                },
                'choice_label' => 'level',
                'required'        => false,
                'auto_initialize' => false,
                'mapped' => false,
            ],
        );

        $form->add($builder->getForm());
	}

	public function addCountryField(FormInterface $form, $project): void
    {
        $countries = [];

        $projectCountries = !is_null($project) ? $project->getCountries()->toArray() : [];

        if (!is_null($projectCountries)) {
            foreach ($projectCountries as $country) {
                $countries[] = $country->getId();
            }
        }

        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
            'countries',
            EntityType::class,
            null,
            [
                'class'           => Country::class,
                'label'           => 'Pays',
                'placeholder'     => null === $project ? 'Sélectionnez un projet' : 'Sélectionnez un pays',
                'query_builder'   => function (EntityRepository $er) use ($countries) {
                    return $er->createQueryBuilder('country')
                        ->andWhere('country.id IN (:countries)')
                        ->setParameter('countries', $countries)
                        ->orderBy('country.name', 'ASC');

                },
                'choice_label' => 'name',
                'required'        => false,
                'auto_initialize' => false,
                'multiple' => true,
                'expanded' => false,
                'mapped' => false,
            ],
        );

        $form->add($builder->getForm());
    }

    public function addCenterField(FormInterface $form, $project): void
    {
        $centers = [];

        $projectCenters = !is_null($project) ? $project->getCenters()->toArray() : [];

        if (!is_null($projectCenters)) {
            foreach ($projectCenters as $projectCenter) {
                $centers[] = $projectCenter->getId();
            }
        }

        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
            'centers',
            EntityType::class,
            null,
            [
                'class'           => Center::class,
                'label'           => 'Centre(s)',
                'placeholder'     => null === $project ? 'Sélectionnez un projet' : 'Sélectionnez un centre',
                'query_builder'   => function (EntityRepository $er) use ($centers) {
                    return $er->createQueryBuilder('center')
                        ->innerJoin('center.centerStatus', 'status')
                        ->where('status.type IN (2, 3)')
                        ->andWhere('center.id IN (:centers)')
                        ->setParameter('centers', $centers)
                        ->orderBy('center.name', 'ASC');

                },
                'choice_label' => 'name',
                'required'        => false,
                'auto_initialize' => false,
                'multiple' => true,
                'expanded' => false,
                'mapped' => false,
            ],
        );

        $form->add($builder->getForm());
    }

    public function addAuthorField(FormInterface $form, $project): void
    {
        $users = [];

        $userProjects = !is_null($project) ? $project->getUserProjects()->toArray() : [];

        if (!is_null($userProjects)) {
            foreach ($userProjects as $userProject) {
                $users[] = $userProject->getUser()->getId();
            }
        }

        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
            'author',
            EntityType::class,
            null,
            [
                'class'           => User::class,
                'label'           => 'Auteur',
                'placeholder'     => null === $project ? 'Sélectionnez un project' : 'Sélectionnez un auteur',
                'query_builder'   => function (EntityRepository $er) use ($users) {
                    return $er->createQueryBuilder('user')
                        ->where('user.id IN (:users)')
                        ->andWhere('user.hasAccessEtmf = :etmf')
                        ->setParameter('users', $users)
                        ->setParameter('etmf', true)
                        ->orderBy('user.lastName', 'ASC');

                },
                'choice_label' => 'getFullName',
                'required'        => true,
                'auto_initialize' => false,
            ],
        );

        $form->add($builder->getForm());
    }

    public function addAuthorQaField(FormInterface $form, $project): void
    {
        $users = [];

        $userProjects = !is_null($project) ? $project->getUserProjects()->toArray() : [];

        if (!is_null($userProjects)) {
            foreach ($userProjects as $userProject) {
                $users[] = $userProject->getUser()->getId();
            }
        }

        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
            'validatedQaBy',
            EntityType::class,
            null,
            [
                'class'           => User::class,
                'label'           => 'AQ réalisé par',
                'placeholder'     => null === $project ? 'Sélectionnez un project' : 'Sélectionnez un AQ réalisé par',
                'query_builder'   => function (EntityRepository $er) use ($users) {
                    return $er->createQueryBuilder('user')
                        ->where('user.id IN (:users)')
                        ->andWhere('user.hasAccessEtmf = :etmf')
                        ->setParameter('users', $users)
                        ->setParameter('etmf', true)
                        ->orderBy('user.lastName', 'ASC');

                },
                'choice_label' => 'getFullName',
                'required'        => false,
                'auto_initialize' => false,
            ],
        );

        $form->add($builder->getForm());
    }

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => DocumentVersion::class,
			'csrf_protection' => false
		]);
	}
}
