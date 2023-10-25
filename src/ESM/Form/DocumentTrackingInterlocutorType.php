<?php

namespace App\ESM\Form;

use App\ESM\Entity\Center;
use App\ESM\Entity\DocumentTracking;
use App\ESM\Entity\DocumentTrackingInterlocutor;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DocumentTrackingInterlocutorType
 * @package App\ESM\Form
 */
class DocumentTrackingInterlocutorType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $centerCountries = [];
        $centers = $this->em->getRepository(Center::class)
            ->findByInterlocutorProject($options['project'], $options['interlocutor']);
        foreach ($centers as $center) {
            foreach ($center->getInstitutions() as $institution) {
                if (!in_array($institution->getCountry(), $centerCountries)) {
                    $centerCountries[] = $institution->getCountry();
                }
            }
        }

        $alreadyDoc = $this->em->getRepository(DocumentTrackingInterlocutor::class)
            ->findBy(['interlocutor' => $options['interlocutor']]);
        $alreadyDoc = array_map(function ($documentTrackingInterlocutor) {
            return $documentTrackingInterlocutor->getDocumentTracking()->getId();
        }, $alreadyDoc);

        $docNoCountries = $this->em->getRepository(DocumentTracking::class)
            ->findBy(['project' => $options['project'], 'country' => null]);
        $docSameCountries = $this->em->getRepository(DocumentTracking::class)
            ->findBy(['project' => $options['project'], 'country' => $centerCountries]);
        $docNoCountries = array_map(function ($documentTracking) {
            return $documentTracking->getId();
        }, $docNoCountries);
        $docSameCountries = array_map(function ($documentTracking) {
            return $documentTracking->getId();
        }, $docSameCountries);
        $docSameCountries = array_merge($docSameCountries, $docNoCountries);

        $builder
            ->add('documentTracking', EntityType::class, [
                'class' => DocumentTracking::class,
                'label' => 'entity.DocumentTracking.field.name',
                'attr' => [
                    'placeholder' => 'entity.DocumentTracking.field.name',
                ],
                'disabled' => !$options['isCreating'],
                'group_by' => function ($choice, $key, $value) {
                    $r = $choice->getTitle();
                    if (null !== $choice->getCountry()) {
                        $r .= ' ('.$choice->getCountry()->getName().')';
                    }

                    return $r;
                },
                'placeholder' => '<<Document>>',
                'choice_label' => function ($documentTracking) {
                    return $documentTracking->getTitle().' '.$documentTracking->getVersion();
                },
                'query_builder' => function (EntityRepository $er) use ($options, $alreadyDoc, $docSameCountries) {
                    $qb = $er->createQueryBuilder('dt')
                        ->where('dt.project = :project')
                        ->setParameter('project', $options['project'])
                        ->andWhere('dt.level = :invLevel')
                        ->setParameter('invLevel', DocumentTracking::levelInterlocutor)
                        ->andWhere('dt.id IN (:inIds)')
                        ->setParameter('inIds', $docSameCountries)
                        ->orderBy('dt.title', 'ASC')
                        ->addOrderBy('dt.version', 'ASC');
                    if ($options['isCreating']) {
                        $qb->andWhere('dt.disabledAt IS NULL');
                        if (count($alreadyDoc) > 0) {
                            $qb->andWhere('dt.id NOT IN (:noInIds)')
                                ->setParameter('noInIds', $alreadyDoc);
                        }
                    }

                    return $qb;
                },
            ])
            ->add('sentAt', DateTimeType::class, [
                'label' => 'entity.DocumentTracking.field.sentAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ])
            ->add('receivedAt', DateTimeType::class, [
                'label' => 'entity.DocumentTracking.field.receivedAt',
                'attr' => [
                    'placeholder' => 'dd/MM/yyyy',
                    'class' => 'js-datepicker',
                ],
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DocumentTrackingInterlocutor::class,
        ]);
        $resolver->setRequired('project');
        $resolver->setRequired('interlocutor');
        $resolver->setRequired('isCreating');
    }
}
