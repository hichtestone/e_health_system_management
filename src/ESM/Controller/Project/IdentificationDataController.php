<?php

namespace App\ESM\Controller\Project;

use App\ESM\Entity\Patient;
use App\ESM\Entity\PatientData;
use App\ESM\Entity\PatientVariable;
use App\ESM\Entity\Project;
use App\ESM\Entity\VariableList;
use App\ESM\Entity\VariableOption;
use App\ESM\Entity\VariableType;
use App\ESM\Entity\Visit;
use App\ESM\Entity\VisitPatient;
use App\ESM\Entity\VisitPatientStatus;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IdentificationDataController.
 *
 * @Route("/projects")
 */
class IdentificationDataController extends AbstractController
{
    /**
     * @Route("/{id}/settings/variable", name="project.settings.variable.index", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('IDENTIFICATIONVARIABLE_LIST')")
     */
    public function index(Project $project): Response
    {
        return $this->render('project/identification_data/variable/index.html.twig', []);
    }

    /**
     * @Route("/{id}/settings/variable/json", name="project.settings.variable.index.json", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('IDENTIFICATIONVARIABLE_LIST')")
     */
    public function jsonIndex(Project $project): Response
    {
		$em = $this->getDoctrine()->getManager();

        $patientVariables = $em->getRepository(PatientVariable::class)->findBy(['project' => $project], ['id' => 'DESC']);

        return $this->json(array_map(function ($variable) use ($em) {
            $canDeleted = false;
            $patientsData = $em->getRepository(PatientData::class)->findBy(['variable' => $variable]);
            foreach ($patientsData as $patientData) {
                if ('' !== $patientData->getVariableValue()) {
                    $canDeleted = true;
                }
            }

            // Si variable utilisée dans le conditionnement et que le conditionnement est activé - bloquer la suppression
            if (!empty($variable->getSchemaConditions())) {
                foreach ($variable->getSchemaConditions() as $schemaCondition) {
                    if (!$schemaCondition->getDisabled()) {
                        $canDeleted = true;
                    }
                }
            }

            if ($variable->isVisit()) {
            	$position = $variable->getVisit() ? $variable->getVisit()->getPosition() : null;
			}

			if ($variable->isExam()) {
				$position = $variable->getExam() ? $variable->getExam()->getPosition() : null;
			}

			if ($variable->isVariable()) {
				$position  = $variable->getPosition();
			}

            return [
                'id' => $variable->getId(),
                'label' => $variable->getLabel(),
                'position' => $position ?? null,
                'source' => $variable->getSourceId(),
                'hasPatient' => $variable->isHasPatient(),
                'hasVisit' => $variable->isHasVisit(),
                'isVisit' => $variable->isVisit(),
                'isExam' => $variable->isExam(),
                'isVariable' => $variable->isVariable(),
                'deletedAt' => (null === $variable->getDeletedAt()) ? 'non' : 'oui',
                'canDeleted' => $canDeleted,
                'variableType' => array_map(function ($type) use ($variable) {
                    // Variable type Liste => on récupere le nom de la liste
                    if (4 === $type->getId()) {
                        return [
                            'id' => $type->getId(),
                            'label' => null != $variable->getVariableList() ? $variable->getVariableList()->getLabel() : '',
                            'listId' => null != $variable->getVariableList() ? $variable->getVariableList()->getId() : 0,
                            'type' => $type->getLabel(),
                        ];
                    } else {
                        return [
                            'id' => $type->getId(),
                            'label' => $type->getLabel(),
                        ];
                    }
                }, [$variable->getVariableType()]),
            ];
        }, $patientVariables));
    }

    /**
     * @Route("/{id}/settings/variable/{name}/doublant", name="project.settings.variable.doublant", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PATIENTTRACKING_LIST')")
     */
    public function jsonCheckUnicity(Project $project, Request $request): Response
    {
		$em = $this->getDoctrine()->getManager();

		$count = $em->getRepository(PatientVariable::class)->countPatientVariable($project->getId(), $request->get('name'));

        return $this->json(['count' => $count]);
    }

    /**
     * @Route("/{id}/settings/variable/visit", name="project.settings.variable.visit.json", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('IDENTIFICATIONVARIABLE_LIST')")
     */
    public function jsonVariableVisit(Project $project): Response
    {
		$em = $this->getDoctrine()->getManager();

		$variableVisit = $em->getRepository(PatientVariable::class)
            ->findBy(['project' => $project, 'isVisit' => true], ['id' => 'DESC']);

        return $this->json(array_map(function ($variable) {
            ($variable->getVisits()->toArray());

            return [
                'id' => $variable->getId(),
                'label' => $variable->getVisits(),
                'source' => $variable->getSourceId(),
                'hasPatient' => $variable->isHasPatient(),
                'hasVisit' => $variable->isHasVisit(),
                'isVisit' => $variable->isVisit(),
                'variableType' => array_map(function ($type) {
                    return [
                        'id' => $type->getId(),
                        'label' => $type->getLabel(),
                    ];
                }, [$variable->getVariableType()]),
            ];
        }, $variableVisit));
    }

	/**
	 * @Route("/{id}/settings/variable/{idPatientVariable}/edit", name="project.settings.variable.edit", methods="PUT", requirements={"id"="\d+", "idPatientVariable"="\d+"})
	 * @ParamConverter("patientVariable", options={"id"="idPatientVariable"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('IDENTIFICATIONVARIABLE_WRITE')")
	 *
	 * @param Request $request
	 * @param PatientVariable $patientVariable
	 * @param Project $project
	 * @return JsonResponse
	 *
	 * @throws Exception
	 */
    public function edit(Request $request, PatientVariable $patientVariable, Project $project): JsonResponse
	{
		$em = $this->getDoctrine()->getManager();

		$data = json_decode($request->getContent(), true);

        // Mise à jour patient Variable
        $patientVariable->setHasPatient($data['item']['hasPatient']);
        $patientVariable->setHasVisit($data['item']['hasVisit']);
        $patientVariable->setSourceId($data['item']['source'] ?? '');
        $patientVariable->setPosition($data['item']['position'] ?? null);
        $patientVariable->setLabel($data['item']['label']);

		if (array_key_exists('format', $data['item'])) {
			$variableType = $em->getRepository(VariableType::class)->findOneBy(['label' => $data['item']['format']]);
			$patientVariable->setVariableType($variableType);
		}

		$em->persist($patientVariable);
        $em->flush();

        // Pour chaque patient créer on rajoute une ou plusieurs lignes dans PatientData en fonction des variables
        // cochées dans la colonne "Afficher dans la liste patients" d'identification des données eCRF
        $patients = $em->getRepository(Patient::class)->findBy(['project' => $project]);

        foreach ($patients as $patient) {
            // Si on coche "Afficher dans la liste patients"
            if (true === $data['item']['hasPatient']) {
                if (null == ($patientData = $em->getRepository(PatientData::class)->findBy(['patient' => $patient, 'variable' => $patientVariable]))) {
                    $patientData = new PatientData();

                    $patientData->setPatient($patient);
                    $patientData->setVariable($patientVariable);

                    $visitPatient = $em->getRepository(VisitPatient::class)->findOneBy(['patient' => $patient, 'variable' => $patientVariable]);

                    if ($visitPatient) {
                        $patientData->setVariableValue(date_format($visitPatient->getOccuredAt(), 'Y-m-d'));
                    } else {
                        $patientData->setVariableValue('');
                    }

                    $patientData->setOrdre(1);

                    $em->persist($patientData);

                    $patientData = $em->getRepository(PatientData::class)->findOneBy(['patient' => $patient, 'variable' => $patientVariable]);
                } else {
                    // Sinon on la met disabledAt à null pour afficher
                    $patientData = $em->getRepository(PatientData::class)->findOneBy(['patient' => $patient, 'variable' => $patientVariable]);
                    if ($patientData) {
                        $patientData->setDisabledAt(null);
                        $em->persist($patientData);
                    }
                }
            } elseif (false === $data['item']['hasPatient']) {
                // Si on décoche "Afficher dans la liste patients"
                // on enleve la variable (disabledAt = current date)
                $patientData = $em->getRepository(PatientData::class)->findOneBy(['patient' => $patient, 'variable' => $patientVariable]);
                if ($patientData) {
                    $patientData->setDisabledAt(new \DateTime());
                    $em->persist($patientData);
                }
            }

            $em->flush();

            if (true === $data['item']['hasVisit']) {
                // Sinon on la met disabledAt à null pour afficher
                if (null == ($visitPatient = $em->getRepository(VisitPatient::class)->findBy(['patient' => $patient, 'variable' => $patientVariable]))) {
                    $visitPatient = new VisitPatient();

                    $visitPatient->setPatient($patient);
                    $visit = $em->getRepository(Visit::class)->findOneBy(['short' => $data['item']['label']]);
                    $visitPatient->setVisit($visit);
                    $visitPatient->setVariable($patientVariable);
                    $status = $em->getRepository(VisitPatientStatus::class)->find(4);
                    $visitPatient->setStatus($status);
                    $visitPatient->setBadge('');
                    $visitPatient->setIteration(1);
                    $visitPatient->setSourceId('');

                    $em->persist($visitPatient);
                    $em->flush();
                } else {
                    // Calcule Dates
                    $visitPatient = $em->getRepository(VisitPatient::class)->findOneBy(['patient' => $patient, 'variable' => $patientVariable]);

                    if ($visitPatient) {
                        $visitPatient->setDisabledAt(null);
                        if (isset($patientData)) {
                            if ('' !== $patientData->getVariableValue()) {
                                $visitPatient->setOccuredAt(new \DateTime($patientData->getVariableValue()));
                            }
                        }
                        $em->persist($visitPatient);
                    }
                }
            } elseif (false === $data['item']['hasVisit']) {
                // Si on décoche "Afficher dans la liste vistes"
                // on enleve la variable (disabledAt = current date)
                $visitPatient = $em->getRepository(VisitPatient::class)->findOneBy(['patient' => $patient, 'variable' => $patientVariable]);
                $patientData = $em->getRepository(PatientData::class)->findOneBy(['patient' => $patient, 'variable' => $patientVariable]);

                if ($visitPatient) {
                    $visitPatient->setDisabledAt(new \DateTime());
                    if ($patientData) {
                        if ('' !== $patientData->getVariableValue()) {
                            $visitPatient->setOccuredAt(new \DateTime($patientData->getVariableValue()));
                        }
                    }
                    $em->persist($visitPatient);
                }
            }

            $em->flush();

            // SET ORDER
            $patientData = $em->getRepository(PatientData::class)->findBy(['patient' => $patient]);

            foreach ($patientData as $entity) {
                if ('Date de signature du consentement' === $entity->getVariable()->getLabel()) {
                    $entity->setOrdre(1);
                } elseif ('Date d\'inclusion' === $entity->getVariable()->getLabel()) {
                    $entity->setOrdre(2);
                } elseif (true === $entity->getVariable()->isVisit()) {
                    $entity->setOrdre(3);
                } elseif (true === $entity->getVariable()->isExam()) {
                    $entity->setOrdre(4);
                } else {
                    $entity->setOrdre(5);
                }

                $em->persist($entity);
            }

            $em->flush();
        }

        $status = 1;
        $msg = 'ok';

        return $this->json([
            'status' => $status,
            'msg' 	 => $msg,
        ]);
    }

	/**
	 * @Route("/{id}/settings/variable/new", name="project.settings.variable.new", methods="POST", requirements={"id"="\d+"})
	 *
	 * @param Request $request
	 * @param Project $project
	 * @param \App\ESM\Service\Entity\PatientData $entityPatientData
	 * @return JsonResponse
	 */
    public function new(Request $request, Project $project, \App\ESM\Service\Entity\PatientData $entityPatientData): JsonResponse
	{
		$em = $this->getDoctrine()->getManager();

		$data = json_decode($request->getContent(), true);

        $variableType = $em->getRepository(VariableType::class)->findOneBy(['label' => $data['data']['format']]);

        $patientVariable = new PatientVariable();

        $patientVariable->setProject($project);
        $patientVariable->setVariableType($variableType);
        $patientVariable->setLabel((string) $data['data']['label']);
        $patientVariable->setPosition((int) $data['data']['position']);
        $patientVariable->setIsVariable(true);
        $patientVariable->setHasPatient(isset($data['data']['hasPatient']) ? $data['data']['hasPatient'] : false);

        $em->persist($patientVariable);

        // SI ON COCHE "AFFICHER CETTE VARIABLE DANS LA LISTE DE PATIENTS"
        $patients = $em->getRepository(Patient::class)->findBy(['project' => $project]);

        foreach ($patients as $patient) {
            // Si on coche "Afficher dans la liste patients"
            if ($data['data']['hasPatient']) {
                // Si la variable n'existe pas on l'a créée
                if (null == ($patientData = $em->getRepository(PatientData::class)->findBy(['patient' => $patient, 'variable' => $patientVariable]))) {
                    $patientData = new PatientData();

                    $patientData->setPatient($patient);
                    $patientData->setVariable($patientVariable);
                    $patientData->setVariableValue('');
                    $patientData->setOrdre(1);
                    $patientData->setDeletedAt($entityPatientData->isOtherDataArchived($patient->getId()) ? new \DateTime() : null);

                    $em->persist($patientData);
                }
            }
        }

        if ('list' == $variableType->getLabel()) {
            $select_name = $data['data']['list']['select'];

            // Nouvelle liste
            $is_new_list = 'Une nouvelle Liste' === $select_name;

            if ($is_new_list) {
                // Creation variable_list
                $variableList = new VariableList();
                $variableList->setLabel('' === $data['data']['list']['name'] ? $data['data']['list']['select'] : $data['data']['list']['name']);
                $em->persist($variableList);

                // creation variable_option
                foreach ($data['data']['list']['option'] as $option) {
                    $variableOption = new VariableOption();
                    $variableOption->setList($variableList);
                    $variableOption->setLabel($option['label']);
                    $variableOption->setCode($option['value']);

                    $em->persist($variableOption);
                }

                // Update patient_variable
                $patientVariable->setVariableList($variableList);
            }

            // SI liste existante - Update patient_variable
            if (!$is_new_list) {
                $variableList = $this->getDoctrine()->getRepository(VariableList::class)->findOneBy([
                    'label' => $select_name,
                ]);
                $patientVariable->setVariableList($variableList);
            }
        }

        $em->flush();

        // SET ORDER
        $patients = $em->getRepository(Patient::class)->findBy(['project' => $project]);
        foreach ($patients as $patient) {
            $patientsData = $em->getRepository(PatientData::class)->findBy(['patient' => $patient]);

            foreach ($patientsData as $entity) {
                if ('Date de signature du consentement' === $entity->getVariable()->getLabel()) {
                    $entity->setOrdre(1);
                } elseif ('Date d\'inclusion' === $entity->getVariable()->getLabel()) {
                    $entity->setOrdre(2);
                } elseif (true === $entity->getVariable()->isVisit()) {
                    $entity->setOrdre(3);
                } elseif (true === $entity->getVariable()->isExam()) {
                    $entity->setOrdre(4);
                } else {
                    $entity->setOrdre(5);
                }

                $em->persist($entity);
            }
        }

        $em->flush();

        $status = 1;
        $msg = 'ok';

        return $this->json([
            'status' => $status,
            'msg' => $msg,
        ]);
    }

	/**
	 * @Route("/{id}/settings/variable/list-options/{name}/show", name="project.settings.variable.listOptions.show", methods="GET", requirements={"id"="\d+"})
	 * @Security("is_granted('PROJECT_ACCESS', project)")
	 *
	 * @param Request $request
	 * @param Project $project
	 * @return JsonResponse
	 */
    public function show(Request $request, Project $project): JsonResponse
	{
		$em = $this->getDoctrine()->getManager();

		$options = [];

        $variableList = $em->getRepository(VariableList::class)->findOneBy(['label' => $request->get('name')]);

        if ($variableList) {
            $variableOptions = $em->getRepository(VariableOption::class)->findBy(['list' => $variableList]);

            if ($variableOptions) {
                foreach ($variableOptions as $option) {
                    $options[] = [$option->getLabel() => $option->getCode()];
                }
            }
        }

        $data = [
            'name' => $variableList ? $variableList->getLabel() : '',
            'options' => $options,
        ];

        return $this->json($data, Response::HTTP_OK, []);
    }

    /**
     * @Route("/{id}/settings/variable/{idVariable}/archive", name="project.settings.variable.archive", methods="GET", requirements={"id"="\d+", "idVariable"="\d+"})
     * @ParamConverter("patientVariable", options={"id"="idVariable"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('IDENTIFICATIONVARIABLE_WRITE')")
     */
    public function archive(Project $project, PatientVariable $patientVariable): Response
    {
		$em 	= $this->getDoctrine()->getManager();
		$status = 0;
        $msg 	= 'Error';

        if ($patientVariable->getProject()->getId() === $project->getId()) {
            $patientVariable->setDeletedAt(new \DateTime());
            $em->persist($patientVariable);
            $em->flush();

            $status = 1;
            $msg = 'ok';
        }

        return $this->json([
            'status' => $status,
            'msg' => $msg,
        ]);
    }

    /**
     * @Route("/{id}/settings/variable/{idVariable}/restore", name="project.settings.variable.restore", methods="GET", requirements={"id"="\d+", "idVariable"="\d+"})
     * @ParamConverter("patientVariable", options={"id"="idVariable"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('IDENTIFICATIONVARIABLE_WRITE')")
     */
    public function restore(Project $project, PatientVariable $patientVariable): Response
    {
		$em 	= $this->getDoctrine()->getManager();
		$status = 0;
        $msg 	= 'Error';

        if ($patientVariable->getProject()->getId() === $project->getId()) {
            $patientVariable->setDeletedAt(null);
            $em->persist($patientVariable);
            $em->flush();

            $status = 1;
            $msg = 'ok';
        }

        return $this->json([
            'status' => $status,
            'msg' => $msg,
        ]);
    }
}
