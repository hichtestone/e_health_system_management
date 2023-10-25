<?php

namespace App\ESM\Controller\Project;

use App\ESM\Entity\Project;
use App\ESM\Entity\SchemaCondition;
use App\ESM\FormHandler\SchemaConditionHandler;
use App\ESM\ListGen\ConditionnementList;
use App\ESM\Message\ConditionMessage;
use App\ESM\Service\ListGen\ListGenFactory;
use App\ESM\Service\Study\SchemaCondition as SchemaConditionService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ConditionnementController
 * @package App\Controller\Project
 */
class ConditionnementController extends AbstractController
{
    /**
     * @Route("/projects/{id}/conditionnement", name="project.conditionnement.index", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('SCHEMACONDITION_LIST')")
     */
    public function index(Project $project, ListGenFactory $lgm, TranslatorInterface $translator): Response
    {
        $list = $lgm->getListGen(ConditionnementList::class);

        return $this->render('project/conditionnement/index.html.twig', [
            'list' 		=> $list->getList($project, $translator),
            'project' 	=> $project,
        ]);
    }

	/**
	 * @Route("/projects/{id}/conditionnement/ajax", name="project.conditionnement.index.ajax", requirements={"id"="\d+"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('SCHEMACONDITION_LIST')")
	 *
	 * @param Request $request
	 * @param ListGenFactory $lgm
	 * @param Project $project
	 * @param TranslatorInterface $translator
	 * @return Response
	 */
    public function indexAjax(Request $request, ListGenFactory $lgm, Project $project, TranslatorInterface $translator): Response
	{
        $list = $lgm->getListGen(ConditionnementList::class);
        $list = $list->getList($project, $translator);
        $list->setRequestParams($request->query);

        return $list->generateResponse();
    }

    /**
     * @Route("/projects/{id}/conditionnement/new", name="project.conditionnement.new", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('IDENTIFICATIONVARIABLE_WRITE')")
     */
    public function new(Project $project, SchemaConditionService $schemaConditionService, SchemaConditionHandler $schemaConditionHandler, Request $request): Response
    {
        // Filtres des conditions
        $data = $schemaConditionService->getFilters($project);

        $entity = new SchemaCondition();
        $entity->setProject($project);
        $entity->setDisabled(true);
        $entity->setLabel('[vide]');

        if ($schemaConditionHandler->handle($request, $entity, ['project' => $project])) {
            return $this->redirectToRoute('project.conditionnement.index', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('project/conditionnement/new_edit.html.twig', [
            'edit' 			=> false,
            'action' 		=> 'create',
            'form' 			=> $schemaConditionHandler->createView(),
            'querybuilder' 	=> $data,
            'condition' 	=> $entity,
        ]);
    }

    /**
     * @Route("/projects/{id}/conditionnement/{condition}/show", name="project.conditionnement.show")
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('SCHEMACONDITION_LIST')")
     */
    public function show(Project $project, SchemaCondition $condition, SchemaConditionService $schemaConditionService): Response
    {
        // Filtres des conditions
        $data = $schemaConditionService->getFilters($project);

        return $this->render('project/conditionnement/show.html.twig', [
            'querybuilder' 	=> $data,
            'condition' 	=> $condition,
        ]);
    }

    /**
     * @Route("/projects/{id}/conditionnement/{condition}/edit", name="project.conditionnement.edit")
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('IDENTIFICATIONVARIABLE_WRITE')")
     */
    public function edit(Project $project, SchemaCondition $condition, SchemaConditionService $schemaConditionService, SchemaConditionHandler $schemaConditionHandler, Request $request, MessageBusInterface $messageBus): Response
    {
        // Filtres des conditions
        $data = $schemaConditionService->getFilters($project);

        if ($schemaConditionHandler->handle($request, $condition, ['project' => $project])) {

            //  symfony/messenger
            $message = new ConditionMessage($condition->getId());
            $messageBus->dispatch($message);

            return $this->redirectToRoute('project.conditionnement.index', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('project/conditionnement/new_edit.html.twig', [
            'edit' 			=> true,
            'action' 		=> 'edit',
            'form' 			=> $schemaConditionHandler->createView(),
            'querybuilder' 	=> $data,
            'condition' 	=> $condition,
        ]);
    }

    /**
     * @Route("/projects/{id}/conditionnement/{condition}/enable_disable", name="project.conditionnement.enable_disable")
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('IDENTIFICATIONVARIABLE_WRITE')")
     */
    public function enable_disable(Project $project, SchemaCondition $condition, ListGenFactory $lgm, TranslatorInterface $translator, Request $request, MessageBusInterface $messageBus): Response
    {
        $list = $lgm->getListGen(ConditionnementList::class);
        $list = $list->getList($project, $translator);

        // toggle enable_disable
        $new_status = '';
        $is_disabled = $condition->getDisabled();
        if ($is_disabled) {
            $condition->setDisabled(false);
            $new_status = 'enabled';
        } else {
            $condition->setDisabled(true);
            $new_status = 'disabled';
        }

        $this->getDoctrine()->getManager()->flush();

        $r = [
            'status' => 1,
            'msg' => 'ok',
            'html' => [
                'tr' => $list->renderRowAjax(['sc.id' => $condition->getId()]),
                'afterUpdEffect' => $list->getAfterAffectAjax($request->get('actionpos')),
            ],
        ];

        //  symfony/messenger
        $message = new ConditionMessage($condition->getId());
        $messageBus->dispatch($message);

        $response = new Response(json_encode($r));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/projects/{id}/conditionnement/{condition}/copy", name="project.conditionnement.copy")
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('IDENTIFICATIONVARIABLE_WRITE')")
     */
    public function copy(Project $project, SchemaCondition $condition, TranslatorInterface $translator): Response
    {
        $condition = clone $condition;

        $condition->setLabel($condition->getLabel().' ('.$translator->trans('word.text_copy').')');

        $condition->setDisabled(true);

        $this->getDoctrine()->getManager()->persist($condition);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('project.conditionnement.index', [
            'id' => $project->getId(),
        ]);
    }
}
