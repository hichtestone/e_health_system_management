<?php

namespace App\ESM\Controller\Project;

use App\ESM\Entity\PatientVariable;
use App\ESM\Entity\PhaseSetting;
use App\ESM\Entity\Project;
use App\ESM\Entity\SchemaCondition;
use App\ESM\Entity\VariableList;
use App\ESM\Entity\VariableOption;
use App\ESM\Entity\Visit;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SchemaConditionController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/projects/{id}/settings/schema-condition", name="project.settings.old_schema_condition.page", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('SCHEMACONDITION_LIST')")
     */
    public function page(Project $project): Response
    {
        $variables = $this->entityManager->getRepository(PatientVariable::class)
            ->findBy(['project' => $project, 'deletedAt' => null], ['id' => 'ASC']);

        $array = $this->json(
            array_map(function ($variable) {
                $variableList = $this->entityManager->getRepository(VariableList::class)->findOneBy(['patient' => $variable]);

                return [
                    'type' => 'string',
                    'id' => $variable->getLabel(),
                    'label' => $variable->getLabel(),
                    'inputType' => $variable->getVariableType()->getLabel(),
                    'inputId' => $variable->getId(),
                    'inputName' => (null !== $variableList) ? $variableList->getLabel() : '',
                ];
            }, $variables)
        );

        return $this->render('project/settings/schema_condition/index.html.twig', [
            'variables' => $array->getContent(),
        ]);
    }


    /**
     * @Route("/ajax/projects/{id}/settings/schema-condition", name="project.settings.schema_condition.index", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('SCHEMACONDITION_LIST')")
     */
    public function index(Project $project): Response
    {
        $conditions = $this->entityManager->getRepository(SchemaCondition::class)
            ->findBy(['project' => $project, 'deletedAt' => null], ['label' => 'ASC']);

        return $this->json(array_map(function ($condition) {
            return [
                'id' => $condition->getId(),
                'label' => $condition->getLabel(),
                'condition' => $condition->getCondition(),
                'visits' => array_map(function ($visit) {
                    return [
                        'id' => $visit->getId(),
                        'label' => $visit->getLabel(),
                        'phase' => [
                            'id' => $visit->getPhase()->getId(),
                            'label' => $visit->getPhase()->getLabel(),
                        ],
                    ];
                }, $condition->getVisits()->toArray()),
                'phases' => array_map(function ($phase) {
                    return [
                        'id' => $phase->getId(),
                        'label' => $phase->getLabel(),
                    ];
                }, $condition->getPhases()->toArray()),
            ];
        }, $conditions));
    }

    /**
     * @Route("/ajax/projects/{id}/settings/schema-condition/{idPatientVariable}/variable-list", name="project.settings.schema_condition.variable_list", methods="GET", requirements={"id"="\d+", "idPatientVariable"="\d+"})
     * @ParamConverter("patientVariable", options={"id"="idPatientVariable"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('SCHEMACONDITION_LIST')")
     *
     * @return JsonResponse
     */
    public function list(Request $request, Project $project, PatientVariable $patientVariable)
    {
        $options = [];

        $variableList = $this->entityManager->getRepository(VariableList::class)->findOneBy(['patient' => $patientVariable]);

        $variableOptions = $this->entityManager->getRepository(VariableOption::class)->findBy(['list' => $variableList]);

        if ($variableOptions) {
            foreach ($variableOptions as $option) {
                array_push($options, [
                    'label' => $option->getLabel(),
                    'value' => $option->getCode(),
                ]);
            }
        }

        $data = [
            'name' => $variableList->getLabel(),
            'options' => $options,
        ];

        return $this->json($data, Response::HTTP_OK, []);
    }

    /**
     * @Route("/ajax/projects/{id}/settings/schema-condition/{idCondition}/delete", name="project.settings.schema_condition.delete", methods="GET", requirements={"id"="\d+", "idCondition"="\d+"})
     * @ParamConverter("schemaCondition", options={"id"="idCondition"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('SCHEMACONDITION_ARCHIVE', schemaCondition)")
     */
    public function delete(Project $project, SchemaCondition $schemaCondition): Response
    {
        $status = 0;
        $msg = 'Error';

        if ($schemaCondition->getProject()->getId() === $project->getId()) {
            $schemaCondition->setDeletedAt(new \DateTime());
            //$this->entityManager->remove($schemaCondition);
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
     * @Route("/ajax/projects/{id}/settings/schema-condition/{idCondition}/update", name="project.settings.schema_condition.update", methods="POST", requirements={"id"="\d+", "idCondition"="\d+"})
     * @ParamConverter("schemaCondition", options={"id"="idCondition"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('SCHEMACONDITION_EDIT', schemaCondition)")
     */
    public function update(Request $request, Project $project, SchemaCondition $schemaCondition): Response
    {
        $status = 0;
        $msg = 'Error';

        if ($schemaCondition->getProject()->getId() === $project->getId()) {
            $params = json_decode($request->getContent(), true);
            $phaseIds = array_map(function ($phase) {
                return $phase->getId();
            }, $schemaCondition->getPhases()->toArray());
            $visitIds = array_map(function ($visit) {
                return $visit->getId();
            }, $schemaCondition->getVisits()->toArray());

            $schemaCondition->setLabel($params['data']['label']);
            $schemaCondition->setCondition($params['data']['condition']);

            // phases
            $newPhaseIds = [];
            foreach ($params['data']['phases'] as $phaseData) {
                $newPhaseIds[] = $phaseData['id'];
                if (!in_array($phaseData['id'], $phaseIds)) {
                    $phase = $this->entityManager->getRepository(PhaseSetting::class)->find($phaseData['id']);
                    $schemaCondition->addPhase($phase);
                }
            }
            foreach ($schemaCondition->getPhases() as $phase) {
                if (!in_array($phase->getId(), $newPhaseIds)) {
                    $schemaCondition->removePhase($phase);
                }
            }

            // visits
            $newVisitIds = [];
            foreach ($params['data']['visits'] as $visitData) {
                $newVisitIds[] = $visitData['id'];
                if (!in_array($visitData['id'], $visitIds)) {
                    $visit = $this->entityManager->getRepository(Visit::class)->find($visitData['id']);
                    $schemaCondition->addVisit($visit);
                }
            }
            foreach ($schemaCondition->getVisits() as $visit) {
                if (!in_array($visit->getId(), $newVisitIds)) {
                    $schemaCondition->removeVisit($visit);
                }
            }

            $this->entityManager->persist($schemaCondition);
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
     * @Route("/ajax/projects/{id}/settings/schema-condition/new", name="project.settings.schema_condition.new", methods="POST", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('SCHEMACONDITION_CREATE')")
     */
    public function create(Request $request, Project $project): Response
    {
        $status = 0;
        $msg = 'Error';

        $schemaCondition = new SchemaCondition();
        $schemaCondition->setProject($project);

        $params = json_decode($request->getContent(), true);

        $schemaCondition->setLabel($params['data']['label']);
        $schemaCondition->setCondition($params['data']['condition']);

        // phases
        foreach ($params['data']['phases'] as $phaseData) {
            $phase = $this->entityManager->getRepository(PhaseSetting::class)->find($phaseData['id']);
            $schemaCondition->addPhase($phase);
        }

        // visits
        foreach ($params['data']['visits'] as $visitData) {
            $visit = $this->entityManager->getRepository(Visit::class)->find($visitData['id']);
            $schemaCondition->addVisit($visit);
        }

        $this->entityManager->persist($schemaCondition);
        $this->entityManager->flush();


        $status = 1;
        $msg = 'ok';

        return $this->json([
            'status' => $status,
            'msg' => $msg,
        ]);
    }
}
