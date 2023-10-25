<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Date;
use App\ESM\Entity\Project;
use App\ESM\FormHandler\DateHandler;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects")
 */
class DateController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

	/**
	 *
	 * @Route("/{id}/dates", name="project.list.dates", methods="GET", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @Security("is_granted('PROJECT_ACCESS', project)")
	 *
	 * @param Request $request
	 * @param Project $project
	 * @return RedirectResponse|Response
	 */
    public function index(Request $request, Project $project)
    {
        if (!$this->isGranted('DATE_SHOW')) {
            if ($this->isGranted('SUBMISSION_LIST')) {
                return $this->redirectToRoute('project.list.submissions', ['id' => $project->getId()]);
            } elseif ($this->isGranted('RULE_SHOW')) {
                return $this->redirectToRoute('project.list.rule', ['id' => $project->getId()]);
            } else {
                throw new AccessDeniedHttpException('Unhautorized');
            }
        }

        $warnings = [];
        $date = $this->entityManager->getRepository(Date::class)->findOneBy(['project' => $project]);

        if ($date) {
        	if (null !== $date->getProject()) {
				if (in_array('0', $date->getProject()->getStudyPopulation(), true)) {
					// pédiatrique
					$m = 6;
				} else {
					// adulte
					$m = 12;
				}

				if (!$this->checkDates($date->getActualLPLVAt(), $date->getFinalReportAnalysedAt(), $m)) {
					$warnings['finalReportAnalysedAt'] = "Cette date doit être dans les $m mois après la date réelle LPLV du CJP";
				}
				if (!$this->checkDates($date->getFinalActualLPLVAt(), $date->getFinalActualReportAt(), $m)) {
					$warnings['finalActualReportAt'] = "Cette date doit être dans les $m mois après la date réelle LPLV final";
				}

				if (!$this->checkDates($date->getActualLPLVAt(), $date->getDepotClinicalTrialsAt(), $m)) {
					$warnings['depotClinicalTrialsAt'] = "Cette date doit être dans les $m mois après la date réelle LPLV du CJP";
				}
				if (!$this->checkDates($date->getFinalActualLPLVAt(), $date->getDepotEudraCtAt(), $m)) {
					$warnings['depotEudraCtAt'] = "Cette date doit être dans les $m mois après la date réelle LPLV final";
				}
			}

        }

        return $this->render('date/index.html.twig', [
            'projectShow' => true,
            'project' => $project,
            'date' => $date,
            'warnings' => $warnings,
        ]);
    }

    private function checkDates(?DateTime $d1, ?DateTime $d2, int $delay): bool
    {
        if (null !== $d1 && null !== $d2) {
            $diff = date_diff($d1, $d2);

            return $diff->format('%m') > $delay || $d1 < $d2;
        }

        return true;
    }

	/**
	 * @Route("/{id}/date/{idDate}/edit", name="project.date.edit", requirements={"id"="\d+",  "idDate":"\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("date", options={"id"="idDate"})
	 * @Security("is_granted('PROJECT_WRITE', project) and is_granted('DATE_EDIT', date)")
	 *
	 * @param Request $request
	 * @param DateHandler $dateHandler
	 * @param Project $project
	 * @param Date $date
	 * @return RedirectResponse|Response
	 */
    public function edit(Request $request, DateHandler $dateHandler, Project $project, Date $date)
    {
        if ($dateHandler->handle($request, $date)) {
            return $this->redirectToRoute('project.list.dates', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('date/create.html.twig', [
            'form' => $dateHandler->createView(),
            'project' => $project,
        ]);
    }
}
