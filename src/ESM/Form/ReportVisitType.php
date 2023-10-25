<?php

namespace App\ESM\Form;

use App\ESM\Entity\ReportConfigVersion;
use App\ESM\Entity\ReportVisit;
use App\ESM\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichFileType;

/**
 * Class ReportVisitType
 * @package App\ESM\Form
 */
class ReportVisitType extends AbstractType
{
	public const ROLE_MONITORING_REPORT_UPLOAD = 'ROLE_MONITORING_REPORT_UPLOAD';

	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        if ($options['report']) {
            $builder
                ->add('reportType', ChoiceType::class, [
                    'label' => 'entity.report_visit.field.report_type.name',
                    'placeholder' => 'entity.report_visit.field.report_type.name',
                    'choices' => array_flip(ReportVisit::REPORT_TYPE),
                    'required' => true,
                ]);
			$builder
				->get('reportType')->addEventListener(
					FormEvents::POST_SUBMIT,
					function (FormEvent $event) {
						$form = $event->getForm();
						$this->addReportConfigVersionField($form->getParent(), $form->getData());
					}
				);
			$builder->addEventListener(
					FormEvents::POST_SET_DATA,
					function (FormEvent $event) {
						$data = $event->getData();
						$reportType = $data->getReportType();
						$form = $event->getForm();
						$this->addReportConfigVersionField($form, $reportType);
					}
			);
			$builder->add('reportedAt', DateTimeType::class, [
                    'label' => 'entity.report_visit.field.createdAt',
                    'attr' => [
                        'placeholder' => 'entity.report_visit.field.createdAt',
                        'class' => 'js-datepicker',
                    ],
                    'html5' => false,
                    'widget' => 'single_text',
                    'required' => true,
                    'format' => 'dd/MM/yyyy',
                ])
            ;
        } elseif ($options['validate']) {
            $builder
                ->add('validatedBy', EntityType::class, [
                    'label' => 'entity.report_visit.field.validatedBy',
                    'class' => User::class,
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('u')
                            ->innerJoin('u.userProjects', 'up')
                            ->innerJoin('u.profile', 'p')
                            ->innerJoin('p.roles', 'r')
                            ->where('up.project = :project')
                            ->andWhere('up.disabledAt IS NULL')
                            ->andWhere('r.code = :role')
                            ->setParameter('project', $options['project'])
                            ->setParameter('role', ReportVisitType::ROLE_MONITORING_REPORT_UPLOAD)
                            ->orderBy('u.lastName', 'ASC');
                    },
                    'choice_label' => 'getFullName',
					'data' => $this->entityManager->getRepository(User::class)->find($options['user']->getId()),
                    'required' => true,
                ])
                ->add('reportFileVich', VichFileType::class, [
                    'label' => 'Document (PDF file)',
                    'required' => true,
                    'allow_delete' => true,
                    'delete_label' => 'Supprimer',
                    'download_link' => false,
                    'asset_helper' => true,
                    'constraints' => [
                        new File([
                            'mimeTypes' => [
                                'application/pdf',
                            ],
                            'mimeTypesMessage' => 'Seul le format(.pdf) est autorisé',
                        ]),
                    ],
                ]);
        } else {
            $builder
                ->add('visitType', ChoiceType::class, [
                    'label' => 'entity.report_visit.field.visit_type.name',
                    'choices' => array_flip(ReportVisit::VISIT_TYPE),
                    'required' => true,
                ])
                ->add('expectedAt', DateTimeType::class, [
                    'label' => 'entity.report_visit.field.expectedAt',
                    'attr' => [
                        'placeholder' => 'entity.report_visit.field.expectedAt',
                        'class' => 'js-datepicker',
                    ],
                    'html5' => false,
                    'widget' => 'single_text',
                    'required' => true,
                    'format' => 'dd/MM/yyyy',
                ])
            ;
        }

		$builder->addEventListener(FormEvents::POST_SUBMIT,  [$this, 'onPostSubmit']);
	}

	public function addReportConfigVersionField(FormInterface $form, $reportType): void
	{
		$builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
			'reportConfigVersion',
			EntityType::class,
			null,
			[
				'label' => 'entity.report_visit.field.report_model',
				'class' => ReportConfigVersion::class,
				'placeholder' =>  null === $reportType ? 'entity.report_visit.field.report_type.name' : 'entity.report_visit.field.report_model',
				'query_builder' => function (EntityRepository $er) use ($form, $reportType) {
				return $er->createQueryBuilder('report_config_version')
					->leftJoin('report_config_version.config', 'report_config')
					->leftJoin('report_config.modelVersion', 'report_model_version')
					->leftJoin('report_model_version.reportModel', 'report_model')
					->where('report_model.visitType = :visit')
					->andWhere('report_model.reportType = :type')
					->andWhere('report_config_version.status = :status')
					->setParameter('visit', $form->getData()->getVisitType())
					->setParameter('type', $reportType)
					->setParameter('status', ReportConfigVersion::STATUS_ACTIVE)
					->groupBy('report_model_version.id')
					->orderBy('report_model.name', 'ASC');
				},
				'choice_label' => 'getModelNameAndPublishedVersion',
				'required' => true,
				'auto_initialize' => false,
			]
		);

		$form->add($builder->getForm());
	}

	public function onPostSubmit(FormEvent $event): void
	{
		$form 		= $event->getForm();
		$entity 	= $event->getData();
		$expectedAt = $entity->getExpectedAt();
		$reportedAt	= $entity->getReportedAt();

		$beginDate 	= new \DateTime('1900-01-01 00:00:00');

		if ($expectedAt < $beginDate) {
			$form->get('expectedAt')->addError(new FormError('limite de date atteinte'));
		}

		if (null !== $reportedAt) {
			if ($reportedAt < $beginDate) {
				$form->get('reportedAt')->addError(new FormError('limite de date atteinte'));
			}
		}
	}

	public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(['data_class' => ReportVisit::class])
            ->setRequired(['project', 'report', 'validate', 'user']);
    }
}
