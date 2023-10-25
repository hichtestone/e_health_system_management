<?php

namespace App\ESM\Controller\Project;

use App\ESM\Entity\Project;
use App\ESM\Entity\SchemaCondition;
use App\ESM\Entity\VisitPatient;
use App\ESM\Entity\VisitPatientStatus;
use App\ESM\Message\ConditionMessage;
use App\ESM\Service\Utils\MonitoringDateAndStatus;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PatientTrackingVisitController.
 * @Route("/projects")
 */
class PatientTrackingVisitController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/{id}/patientTracking/visit", name="project.patientTracking.visit.index", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PATIENTTRACKING_LIST')")
     */
    public function index(Project $project): Response
    {
        return $this->render('project/patient_tracking/visit/index.html.twig', []);
    }

	/**
	 * @Route("/{id}/patientTracking/visit/visitPatient", name="project.patientTracking.visit.visitPatient", methods="GET", requirements={"id"="\d+"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PATIENTTRACKING_LIST')")
	 * @throws Exception
	 */
    public function jsonVisitPatient(Project $project): Response
	{
		$visitsPatient = $this->entityManager->getRepository(VisitPatient::class)->getVisitPatient($project->getId());

		return $this->json(array_map(function ($visit) {
			return [
				'id' => $visit['id'],
				'center' => $visit['center'],
				'patient' => $visit['patient'],
				'visit' => $visit['visit'],
				'ref' => $visit['ref'],
				'reel' => $visit['reel'],
				'phase' => $visit['phase'],
				'monitoredAt' => null !== $visit['monitoredAt'] ? (new \DateTime($visit['monitoredAt']))->format('Y-m-d') : null,
				'occuredAt' => null !== $visit['occuredAt'] ? (new \DateTime($visit['occuredAt']))->format('Y-m-d') : null,
				'status' => $visit['status'],
				'statusLabel' => $visit['statusLabel'],
				'label' => $visit['label'],
				'badge' => $visit['badge'],
			];
		}, $visitsPatient));


    }

    /**
     * @Route("/{id}/patientTracking/visit/visitPatient/export", name="project.patientTracking.visit.visitPatient.export", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PATIENTTRACKING_LIST')")
     */
    public function jsonVisitPatientExport(Project $project): Response
    {
        $visitsPatient = $this->entityManager->getRepository(VisitPatient::class)->getVisitPatient($project->getId());

        return $this->json(array_map(function ($visit) {
            return [
                'N° centre' => $visit['center'],
                'N° patient' => $visit['patient'],
                'Visite' => $visit['visit'],
                'Phase' => $visit['phase'],
                'Date prévisionnelle' => null !== $visit['monitoredAt'] ? (new \DateTime($visit['monitoredAt']))->format('d/m/Y') : null,
                'Date réelle' => null !== $visit['occuredAt'] ? (new \DateTime($visit['occuredAt']))->format('d/m/Y') : null,
                'status' => ('Vide' === $visit['statusLabel']) ? $visit['badge'] : $visit['statusLabel'].' '.$visit['badge'],
            ];
        }, $visitsPatient));
    }

	/**
	 * @Route("/{id}/patientTracking/visit/{idVisit}/edit", name="project.patientTracking.visit.edit", methods="PUT", requirements={"id"="\d+", "idVisit"="\d+"})
	 * @ParamConverter("visitPatient", options={"id"="idVisit"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PATIENTTRACKING_CREATE')")
	 * @throws \JsonException
	 * @throws Exception
	 */
    public function edit(Request $request, Project $project, VisitPatient $visitPatient, MonitoringDateAndStatus $calcul, MessageBusInterface $messageBus): Response
    {
        $status = 0;
        $msg = 'Error';

        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $param = [
            'idPatient' => $visitPatient->getPatient()->getId(),
            'patient' => $data['item']['patient'],
            'center' => $data['item']['center'],
            'variables' => [
                $data['item']['visit'] => ['2', $data['item']['occuredAt']],
            ],
        ];

        $reel = (new \DateTime($param['variables'][$data['item']['visit']][1]))->format('Y-m-d');

        $calcul->monitoringDateAndStatusVisitUpdate($project, $param, $reel, key($param['variables']));
        $conditions = $this->entityManager->getRepository(SchemaCondition::class)->findBy(['disabled' => false]);
        // Liste de conditions
        foreach ($conditions as $condition) {
            //  symfony/messenger
            $message = new ConditionMessage($condition->getId());
            $messageBus->dispatch($message);
        }

        $status = 1;
        $msg = 'Ok';

        return $this->json([
            'status' => $status,
            'msg' => $msg,
        ]);
    }

    /**
     * @Route("/{id}/patientTracking/visit/status", name="project.patientTracking.visit.status", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PATIENTTRACKING_LIST')")
     */
    public function jsonStatus(Project $project): Response
    {
        $visitPatientStatus = $this->entityManager->getRepository(VisitPatientStatus::class)->findBy(['id' => [1, 2]]);

        return $this->json(
            array_map(function ($status) {
                return [
                    'id' => $status->getId(),
                    'label' => $status->getLabel(),
                ];
            }, $visitPatientStatus)
        );
    }

    /**
     * @Route("/{id}/patientTracking/visit/status/edit", name="project.patientTracking.visit.status.edit", methods="PUT", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('PATIENTTRACKING_CREATE')")
     */
    public function jsonStatusEdit(Project $project, Request $request): Response
    {
        $status = 0;
        $msg = 'Error';

        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if ($data) {
            foreach ($data['data'] as $visit) {
                $visitPatient = $this->entityManager->getRepository(VisitPatient::class)->find($visit['id']);
                $status = $this->entityManager->getRepository(VisitPatientStatus::class)->findBy(['label' => $data['status']['label']]);
                $visitPatient->setStatus($status['0']);
                $visitPatient->setBadge('');

                $this->entityManager->persist($visitPatient);
            }

            $this->entityManager->flush();

            $status = 1;
            $msg = 'Ok';
        }

        return $this->json([
            'status' => $status,
            'msg' => $msg,
        ]);
    }

    /**
     * @Route("/{id}/patientTracking/visit/status/update", name="project.patientTracking.visit.status.update", methods="PUT", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('PATIENTTRACKING_CREATE')")
     */
    public function jsonStatusUpdate(Project $project, Request $request): Response
    {
        $status = 0;
        $msg = 'Error';

        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if ($data) {
            $visitPatient = $this->entityManager->getRepository(VisitPatient::class)->find($data['data']['id']);
            if ('à monitorer' === $data['data']['label']) {
                $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(2);
            }

            if ('Monitoré' === $data['data']['label']) {
                $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(1);
            }

            $visitPatient->setStatus($status);

            $this->entityManager->persist($visitPatient);

            $this->entityManager->flush();

            $status = 1;
            $msg = 'Ok';
        }

        return $this->json([
            'status' => $status,
            'msg' => $msg,
        ]);
    }
}
