<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Project;
use App\ESM\Entity\Rule;
use App\ESM\FormHandler\RuleHandler;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects")
 */
class RuleController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

	/**
	 *
	 * @Route("/{id}/rules", name="project.list.rule", methods="GET", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('RULE_SHOW')")
	 *
	 * @param Project $project
	 * @return Response
	 */
    public function index(Project $project)
    {
        $rule = $this->entityManager->getRepository(Rule::class)->findOneBy(['project' => $project]);

        return $this->render('rule/index.html.twig', [
            'project' => $project,
            'rule' => $rule,
        ]);
    }

	/**
	 * @Route("/{id}/rules/{rule}/edit", name="project.rule.edit", requirements={"id"="\d+", "rule"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("rule", options={"id"="rule"})
	 * @Security("is_granted('PROJECT_WRITE', project) and is_granted('RULE_EDIT', rule)")
	 *
	 * @param Request $request
	 * @param RuleHandler $ruleHandler
	 * @param Project $project
	 * @param Rule $rule
	 * @return RedirectResponse|Response
	 */
    public function edit(Request $request, RuleHandler $ruleHandler, Project $project, Rule $rule)
    {
        if ($ruleHandler->handle($request, $rule)) {
            return $this->redirectToRoute('project.list.rule', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('rule/create.html.twig', [
            'form' => $ruleHandler->createView(),
            'project' => $project,
        ]);
    }
}
