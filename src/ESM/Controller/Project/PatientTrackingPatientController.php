<?php

namespace App\ESM\Controller\Project;

use App\ESM\Entity\Center;
use App\ESM\Entity\DropdownList\ExamStatus;
use App\ESM\Entity\Patient;
use App\ESM\Entity\PatientData;
use App\ESM\Entity\PatientVariable;
use App\ESM\Entity\Project;
use App\ESM\Entity\SchemaCondition;
use App\ESM\Entity\Visit;
use App\ESM\Entity\VisitPatient;
use App\ESM\Entity\VisitPatientStatus;
use App\ESM\Message\ConditionMessage;
use App\ESM\Service\Utils\ArrayGroupByKeyService;
use App\ESM\Service\Utils\MonitoringDateAndStatus;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PatientTrackingPatientController.
 *
 * @Route("/projects")
 */
class PatientTrackingPatientController extends AbstractController
{
    private $entityManager;

    /**
     *
     * @var array
     */
    private $patients = [];
    private $patientDatas = [];

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/{id}/patientTracking/patient", name="project.patientTracking.patient.index", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('ROLE_CENTER_READ')")
     */
    public function index(Project $project): Response
    {
        return $this->render('project/patient_tracking/patient/index.html.twig', []);
    }

    /**
     * @Route("/{id}/patientTracking/patient/index", name="project.patientTracking.patient.index.json", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PATIENTTRACKING_LIST')")
     */
    public function jsonIndex(Project $project): Response
    {
        $patients = $this->entityManager->getRepository(Patient::class)
            ->findBy(['project' => $project, 'deletedAt' => null], ['number' => 'ASC']);

        return $this->json(array_map(function ($patient) {
            return [
                'id' => $patient->getId(),
                'number' => $patient->getNumber(),
                'consentAt' => $patient->getConsentAt(),
                'inclusionAt' => $patient->getInclusionAt(),
                'center' => array_map(function ($center) {
                    return [
                        'id' => $center->getId(),
                        'name' => $center->getName(),
                        'number' => $center->getNumber(),
                    ];
                }, [$patient->getCenter()]),
            ];
        }, $patients));
    }

    /**
     * @Route("/{id}/patientTracking/patient/center", name="project.patientTracking.patient.center", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PATIENTTRACKING_LIST')")
     */
    public function jsonCenter(Project $project): Response
    {
        $centers = $this->entityManager->getRepository(Center::class)->findBy(['project' => $project]);

        return $this->json(
            array_map(function ($center) {
				if ($center->getCenterStatus()->getType() !== 1) {
					return [
						'id'     => $center->getId(),
						'number' => $center->getNumber(),
					];
				}
            }, $centers)
        );
    }

    /**
     * @Route("/{id}/patientTracking/patient/{name}/list", name="project.patientTracking.patient.list", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PATIENTTRACKING_LIST')")
     */
    public function jsonList(Request $request, Project $project): Response
    {
        $list = $this->entityManager->getRepository(PatientVariable::class)->getVariableByList($request->get('name'));

        return $this->json(
            array_map(function ($option) {
                return [
                    'name' => $option['name'],
                    'label' => $option['label'],
                    'value' => $option['value'],
                ];
            }, $list)
        );
    }

    /**
     * @Route("/{id}/patientTracking/patient/variableChecked", name="project.patientTracking.patient.variableChecked", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PATIENTTRACKING_LIST')")
     */
    public function jsonVariableCheckedIndex(Project $project): Response
    {
        $variablesChecked = $this->entityManager->getRepository(PatientVariable::class)->findBy(['hasPatient' => true, 'project' => $project, 'deletedAt' => null]);

        return $this->json(
            array_map(function ($variable) {
                return [
                    'id' => $variable->getId(),
                    'label' => $variable->getLabel(),
                ];
            }, $variablesChecked)
        );
    }

    /**
     * @Route("/{id}/patientTracking/patient/patientData", name="project.patientTracking.patient.patientData", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PATIENTTRACKING_LIST')")
     * @throws ORMException
     */
	public function jsonPatientData(Project $project, ArrayGroupByKeyService $arrayGroupByKeyService): Response
	{
		$patientData = $this->entityManager->getRepository(PatientData::class)->getPatientData($project->getId());

		$newPatientData = [];
		$grouped = $arrayGroupByKeyService->array_group_by($patientData, 'patient', 'center');


        foreach ($grouped as $patient) {

			foreach ($patient as $key => $value) {

				$temp = [];

				foreach ($value as $data) {

					$temp['center'] 	= $data['center'];
					$temp['patient'] 	= $data['patient'];
					$temp['id'] 		= $data['id'];
					$temp['archived'] 	= $data['archived'];
					$temp['phase']		= $data['phase_label'] ?? '';

					// On archive un patient ses variables n'ont pas vide hors variable = Date de signature du consentement
					$temp['isArchived'] = true;
					$patientEntity = $this->getPatient($data['idPatient']);
					$patientDataEntity = $this->entityManager->getRepository(PatientData::class)->findBy(['patient' => $patientEntity]);
					//$patientDataEntity = $this->getPatientData($patientEntity);

					foreach ($patientDataEntity as $entity) {
						if ('' !== $entity->getVariableValue() && 'Date de signature du consentement' !== $entity->getVariable()->getLabel()) {
							$temp['isArchived'] = false;
						}
					}

					$temp['idPatient'] = $data['idPatient'];
					$temp['variable']['name'][] = $data['variable'];
					$temp['variable']['type'][] = $data['type'];

					if ('4' === $data['type']) {
						$req = $this->entityManager->getRepository(PatientData::class)->getListOption($project->getId(), $data['idVariable']);
						$temp['variable']['value'][] = array_map(function ($v) {return $v; }, $req);
					} elseif ('2' === $data['type']) {
						$temp['variable']['value'][] = $data['value'];
					} else {
						$temp['variable']['value'][] = $data['value'];
					}

					$temp['variable']['id'][] = $data['idVariable'];
				}
				$newPatientData[] = $temp;
			}
		}


		$result = $temp1 = [];
		foreach ($newPatientData as $data) {

			$temp1['idPatient'] 	= $data['idPatient'];
			$temp1['centre'] 		= $data['center'];
			$temp1['patient'] 		= $data['patient'];
			$temp1['isArchived'] 	= $data['isArchived'];
			$temp1['phase']			= $data['phase'] ?? '';

			$temp2 = [];

			// Nombre de donnees remplies
			$valuefilled_count = 0;

			foreach ($data['variable']['name'] as $i => $name) {
				foreach ($data['variable']['value'] as $j => $value) {
					foreach ($data['variable']['type'] as $k => $type) {
						foreach ($data['variable']['id'] as $l => $id) {
							if ($i === $j && $j === $k && $k === $l) {
								if ('4' === $type) {
									$val = $this->entityManager->getRepository(PatientData::class)->getVariable($project->getId(), $data['idPatient'], $id);
									$code = $this->entityManager->getRepository(PatientData::class)->getListOptionLabelCode($project->getId(), $id, $val);
									$temp2[$name] = ['option' => $value, 'value' => $val, 'type' => $type, 'id' => $id, 'code' => isset($code) ? $code : []];
									//$temp2[$name] = ['option' => $value, 'value' => $val, 'type' => $type, 'id' => $id];
									if ('' !== $val) {
										++$valuefilled_count;
									}
								} else {
									$visitPatientEntity = $this->entityManager->getRepository(VisitPatient::class)->getStatusByPatientAndVariable($project->getId(), $data['idPatient'], $id);
									$temp2[$name] = ['value' => $value, 'type' => $type, 'id' => $id, 'status' => $visitPatientEntity];
									if ('' !== $value) {
										++$valuefilled_count;
									}
								}
							}
						}
					}
				}

				$temp1['variable'] = $temp2;
			}

			$temp1['archived'] = (null === $data['archived']) ? 'Non' : 'Oui';
			$temp1['valuefilled_count'] = $valuefilled_count;
			//$valuefilled_count = 0;

			$result[] = array_merge_recursive($temp1, $temp2);
		}

		$patients = [];
		foreach ($result as $key => $subArr) {
			unset($subArr['variable']);
			$patients[$key] = $subArr;
		}

		$final = [];
		foreach ($patients as $patient) {
			$temp = [];
			foreach ($patient as $key => $val) {
				$key = ('archived' === $key) ? 'archivé' : $key;
				$key = ('centre' === $key) ? 'N° centre' : $key;
				$key = ('patient' === $key) ? 'N° patient' : $key;
				if ('array' === gettype($val)) {
					$temp[$key] = $val['value'];
				} else {
					$temp[$key] = $val;
				}
			}
			unset($temp['idPatient']);
			unset($temp['isArchived']);
			unset($temp['valuefilled_count']);
			$final[] = $temp;
		}

		$json = [];
		$json['export'] = $final;
		$json['table'] = $patients;

		return $this->json($json);
	}


    /**
     * @param $patientID
     * @return Patient|null
     * @throws ORMException
     */
    private function getPatient($patientID): ?Patient
    {
        if (!array_key_exists($patientID, $this->patients)) {

            $currentPatient = $this->entityManager->getRepository(Patient::class)->find($patientID);

            if (isset($currentPatient)) {
                $this->patients[$patientID] = $this->entityManager->getReference(Patient::class, $currentPatient->getId());
                $currentPatient = $this->patients[$patientID];
            }

        } else {
            $currentPatient = $this->patients[$patientID];
        }

        return $currentPatient;
    }

    /**
     * @param $patientEntity
     * @return mixed|object|object[]|null
     * @throws ORMException
     */
    private function getPatientData($patientEntity)
    {
        if (!array_key_exists($patientEntity->getId(), $this->patientDatas)) {

            $currentPatientData = $this->entityManager->getRepository(PatientData::class)->findBy(['patient' => $patientEntity]);

            if (isset($currentPatientData)) {

                foreach ($currentPatientData as $obj) {

                    $this->patientDatas[$patientEntity->getId()] = $this->entityManager->getReference(PatientData::class, $obj->getId());
                    $currentPatientData = $this->patientDatas[$patientEntity->getId()];
                }
            }

        } else {
            $currentPatientData = $this->patientDatas[$patientEntity->getId()];
        }

        return $currentPatientData;
    }

    /**
     * @Route("/{id}/patientTracking/{center}/center/{patient}/patient", name="project.patientTracking.patient.variableChecked", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PATIENTTRACKING_LIST')")
     */
    public function jsonCheckHasCenterPatient(Project $project, Request $request): Response
    {
        $count = $this->entityManager->getRepository(Patient::class)->countPatientCenter($project->getId(), $request->get('patient'), $request->get('center'));

        return $this->json(['count' => $count[0]['counter'], 'idPatient' => $count[0]['idPatient']]);
    }

    /**
     * @Route("/{id}/patientTracking/patient/new", name="project.patientTracking.patient.new", methods="POST", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PROJECT_WRITE', project) and is_granted('PATIENTTRACKING_CREATE')")
     */
    public function create(Request $request, Project $project, MonitoringDateAndStatus $calcul, MessageBusInterface $messageBus): Response
    {
        $status = 0;
        $msg = 'Error';

        $patient = new Patient();
        $patient->setProject($project);

        $params = json_decode($request->getContent(), true);

        if ($params) {
            if (null === $params['data']['inclusionAt']) {
                $inclusionAt = null;
            } else {
                $inclusionAt = \DateTime::createFromFormat('Y-m-d', date('Y-m-d', strtotime($params['data']['inclusionAt'])));
            }

            $consentAt = \DateTime::createFromFormat('Y-m-d', date('Y-m-d', strtotime($params['data']['consentAt'])));

            $patient->setNumber($params['data']['patient']);
            $patient->setConsentAt($consentAt);
            $patient->setInclusionAt($inclusionAt);

            // center
            $center = $this->entityManager->getRepository(Center::class)->findOneBy(['number' => $params['data']['center']]);

            $patient->setCenter($center);
            $this->entityManager->persist($patient);

            $patientVariables = $this->entityManager->getRepository(PatientVariable::class)->findBy(['project' => $project]);

            foreach ($patientVariables as $patientVariable) {
                // PatientData
                $patientData = new PatientData();
                $patientData->setPatient($patient);
                $patientData->setVariable($patientVariable);
                $patientData->setOrdre(1);
                // si type = date et 'Date de signature du consentement' ou 'Date d'inclusion'
                if ('date' === $patientVariable->getVariableType()->getLabel()
                    && 'Date de signature du consentement' === $patientVariable->getLabel()) {
                    $patientData->setVariableValue($params['data']['consentAt']);
                } elseif ('date' === $patientVariable->getVariableType()->getLabel()
                    && "Date d'inclusion" === $patientVariable->getLabel()) {
                    $patientData->setVariableValue($params['data']['inclusionAt'] ?? '');
                } elseif ('date' === $patientVariable->getVariableType()->getLabel()) {
                    // si type = date autre $consent et $occured
                    $patientData->setVariableValue('');
                } elseif ('text' === $patientVariable->getVariableType()->getLabel() || 'numeric' === $patientVariable->getVariableType()->getLabel()) {
                    // si type = text ou  type = numeric
                    $patientData->setVariableValue('');
                } else {
                    // si type = list
                    $patientData->setVariableValue('');
                }

                $this->entityManager->persist($patientData);
            }

            $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(4);

            $patientVariablesVisit = $this->entityManager->getRepository(PatientVariable::class)->findBy(['project' => $project, 'hasVisit' => true]);

            if ($patientVariablesVisit) {
                foreach ($patientVariablesVisit as $visitVariable) {
                    $visit = $this->entityManager->getRepository(Visit::class)->findOneBy(['project' => $project, 'short' => $visitVariable->getLabel()]);
                    if ($visit) {
                        // VisitPatient
                        $visitPatient = new VisitPatient();
                        $visitPatient->setPatient($patient);
                        $visitPatient->setVisit($visit);

                        $visitPatient->setVariable($visitVariable);
                        $visitPatient->setStatus($status);
                        $visitPatient->setIteration(1);
                        $visitPatient->setSourceId('');

                        $this->entityManager->persist($visitPatient);
                    }
                }
            }

            $this->entityManager->flush();

            $patient = $this->entityManager->getRepository(Patient::class)->findOneBy([], ['id' => 'DESC']);

            $patientsData = $this->entityManager->getRepository(PatientData::class)->findBy([], ['id' => 'ASC']);

            foreach ($patientsData as $patientData) {
                $params = [
                    'data' => [
                        'idPatient' => $patient->getId(),
                        'patient' => $patient->getNumber(),
                        'center' => $patient->getCenter()->getNumber(),
                        'variables' => [
                            $patientData->getVariable()->getLabel() => ['2', $patientData->getVariableValue()],
                        ],
                    ],
                ];

                foreach ($params['data']['variables'] as $key => $variable) {
                    $calcul->monitoringDateAndStatus($project, $params['data'], $variable['1'], $key, false);
                    $conditions = $this->entityManager->getRepository(SchemaCondition::class)->findBy(['disabled' => false]);
                    // Liste de conditions
                    foreach ($conditions as $condition) {
                        //  symfony/messenger
                        // Application conditions
                        $message = new ConditionMessage($condition->getId());
                        $messageBus->dispatch($message);
                    }
                }
            }

            // SET ORDER
            $patientData = $this->entityManager->getRepository(PatientData::class)->findBy(['patient' => $patient]);

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

                $this->entityManager->persist($entity);
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
     * @Route("/{id}/patientTracking/patient/{idPatient}/update", name="project.patientTracking.patient.update", methods="PUT", requirements={"id"="\d+", "idPatient"="\d+"})
     * @ParamConverter("patient", options={"id"="idPatient"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PATIENTTRACKING_CREATE')")
     */
    public function edit(Request $request, Project $project, Patient $patient, MonitoringDateAndStatus $calcul, MessageBusInterface $messageBus): Response
    {
        $status = 0;
        $msg = 'Error';

        $params = json_decode($request->getContent(), true);

        $inclusionAt = $consentAt = null;
        if ($params) {
            if ('' === $params['data']['variables']['Date de signature du consentement']['1']) {
                $consentAt = null;
            } else {
                $consentAt = \DateTime::createFromFormat('Y-m-d', date('Y-m-d', strtotime($params['data']['variables']['Date de signature du consentement']['1'])));
            }

            if (isset($params['data']['variables']["Date d'inclusion"])) {
                if ('' === $params['data']['variables']["Date d'inclusion"]['1']) {
                    $inclusionAt = null;
                } else {
                    $inclusionAt = \DateTime::createFromFormat('Y-m-d', date('Y-m-d', strtotime($params['data']['variables']["Date d'inclusion"]['1'])));
                }
            }

            $patient->setNumber($params['data']['patient']);

            $center = $this->entityManager->getRepository(Center::class)->findOneBy(['number' => $params['data']['center'], 'project' => $project]);

            $patient->setCenter($center);
            $patient->setConsentAt($consentAt);
            $patient->setInclusionAt($inclusionAt);

            $this->entityManager->persist($patient);
            foreach ($params['data']['variables'] as $key => $variable) {
                $calcul->monitoringDateAndStatus($project, $params['data'], $variable['1'], $key, true);
                $conditions = $this->entityManager->getRepository(SchemaCondition::class)->findBy(['disabled' => false]);
                // Liste de conditions
                foreach ($conditions as $condition) {
                    //  symfony/messenger
                    // Application conditions
                    $message = new ConditionMessage($condition->getId());
                    $messageBus->dispatch($message);
                }

                if (!empty($variable[3])) {
					$examPatientVariable = $this->entityManager->getRepository(ExamPatientVariable::class)->find($variable[3][0]);
					$examPatientVariable->setIsNotDone($variable[3][1]);
					if (!$variable[3][1]) {
						$examPatientVariable->setIsNotDoneReason(null);
					} else {
						$examStatus = $this->entityManager->getRepository(ExamStatus::class)->findOneBy(['label' => $variable[3][2]]);
						$examPatientVariable->setIsNotDoneReason($examStatus);
					}
					$this->entityManager->persist($examPatientVariable);
					$this->entityManager->flush();
				}
            }

            $status = 1;
            $msg = 'Ok';
        }

        return $this->json([
            'status' => $status,
            'msg' => $msg,
        ]);
    }

    /**
     * @Route("/{id}/patientTracking/patient/{idPatient}/archive", name="project.patientTracking.patient.archive", methods="GET", requirements={"id"="\d+", "idPatient"="\d+"})
     * @ParamConverter("patient", options={"id"="idPatient"})
     * @Security("is_granted('PROJECT_ACCESS', project)")
     */
    public function archive(Project $project, Patient $patient): Response
    {
        $status = 0;
        $msg = 'Error';

        if ($patient->getProject()->getId() === $project->getId()) {
            // archive patient
            $patient->setDeletedAt(new \DateTime());
            $this->entityManager->persist($patient);

            // archive patient_data
            $patientData = $this->entityManager->getRepository(PatientData::class)->findBy(['patient' => $patient]);
            foreach ($patientData as $data) {
                $data->setDeletedAt(new \DateTime());
                $this->entityManager->persist($data);
            }

            // archive visit patient
            $visitPatients = $this->entityManager->getRepository(VisitPatient::class)->findBy(['patient' => $patient]);
            foreach ($visitPatients as $visitPatient) {
                $visitPatient->setDeletedAt(new \DateTime());
                $this->entityManager->persist($visitPatient);
            }

            $this->entityManager->flush();
            $status = 1;
            $msg = 'ok';
        }

        return $this->json([
            'status' => $status,
            'msg' => $msg,
        ]);
    }

    /**
     * @Route("/{id}/patientTracking/patient/{idPatient}/restore", name="project.patientTracking.patient.restore", methods="GET", requirements={"id"="\d+", "idPatient"="\d+"})
     * @ParamConverter("patient", options={"id"="idPatient"})
     * @Security("is_granted('PROJECT_ACCESS', project)")
     */
    public function restore(Project $project, Patient $patient, Request $request): Response
    {
        $status = 0;
        $msg = 'Error';

        if ($patient->getProject()->getId() === $project->getId()) {
            // restore patient
            $patient->setDeletedAt(null);
            $this->entityManager->persist($patient);

            // restore patient_data
            $patientData = $this->entityManager->getRepository(PatientData::class)->findBy(['patient' => $patient]);
            foreach ($patientData as $data) {
                $data->setDeletedAt(null);
                $this->entityManager->persist($data);
            }

            // restore visit patient
            $visitPatients = $this->entityManager->getRepository(VisitPatient::class)->findBy(['patient' => $patient]);
            foreach ($visitPatients as $visitPatient) {
                $visitPatient->setDeletedAt(null);
                $this->entityManager->persist($visitPatient);
            }

            $this->entityManager->flush();
            $status = 1;
            $msg = 'ok';
        }

        return $this->json([
            'status' => $status,
            'msg' => $msg,
        ]);
    }

	/**
	 * @Route("/{id}/patientTracking/patient/examStatus", name="project.patientTracking.patient.examStatus", methods="GET", requirements={"id"="\d+"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PATIENTTRACKING_LIST')")
	 */
	public function getExamStatus(Project $project): Response
	{
		$examsStatus = $this->entityManager->getRepository(ExamStatus::class)->findAll();

		return $this->json(
			array_map(function ($examStatus) {
				if (is_null($examStatus->getDeletedAt())) {
					return [
						'id'     => $examStatus->getId(),
						'label' => $examStatus->getLabel(),
					];
				}
			}, $examsStatus)
		);
	}
}
