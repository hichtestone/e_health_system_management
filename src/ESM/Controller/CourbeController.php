<?php

namespace App\ESM\Controller;

use App\ESM\Entity\CourbeSetting;
use App\ESM\Entity\Patient;
use App\ESM\Entity\Point;
use App\ESM\Entity\Project;
use App\ESM\FormHandler\CourbeHandler;
use App\ESM\Repository\CourbeSettingRepository;
use App\ESM\Service\ListGen\ListGenFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects")
 */
class CourbeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/{id}/courbe-settings", name="project.list.courbe.setting", methods="GET", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('ROLE_PROJECT_SETTINGS_READ')")
     */
    public function index(Request $request, ListGenFactory $lgm, Project $project, CourbeSettingRepository $repository): Response
    {
        $courbe = $project->getCourbe();
        //dd($courbe);
        $pointsY = [];
        $nbrepatients = [];
        $v = [];
        $nbrepatient = [];
        if ($courbe) {
            $v = $this->getDoctrine()->getRepository(Point::class)->getPointsByCourbe($courbe);
            $nbrepatient = $this->getDoctrine()->getRepository(Patient::class)->findByDateInclusion($courbe);

            foreach ($v as $point) {
                $pointsY[] = $point->getY();
            }
            foreach ($nbrepatient as $nb) {
                $nbrepatients[] = $nb;
            }
        }

        return $this->render('courbe/index.html.twig', [
            'courbe' => $courbe,
            'project' => $project,
            'points' => $v,
            'nbrepatient' => $nbrepatient,
            'pointsY' => json_encode($pointsY),
            'nbrepatients' => json_encode($nbrepatients),
        ]);
    }

	/**
	 * @Route("/{id}/courbe-settings/{courbe}/show/ajax", name="project.courbe.setting.show.ajax", requirements={"id"="\d+", "courbe"="\d+"}, options={"expose"=true}, methods="GET")
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("courbe", options={"id"="courbe"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('ROLE_PROJECT_SETTINGS_READ')")
	 *
	 * @param Request $request
	 * @param ListGenFactory $lgm
	 * @param Project $project
	 * @param CourbeSetting $courbe
	 * @return Response
	 */
    public function showAjax(Request $request, ListGenFactory $lgm, Project $project, CourbeSetting $courbe): Response
	{
        $formatDate = 'd/m/Y';  // Todo Parametriser

        // Date d'inclusion
        $inclusion_at = $courbe->getDatestrat();

        // Unite
        $unit = $courbe->getUnit();

        // Unite semaine
        $is_week = 'semaine' === $unit;

        // Points
        $points = $courbe->getPoints();

        //récuperer  les points de la courbe paramétrée
        $points_courbe = $this->getDoctrine()->getRepository(Point::class)->getPointsByCourbe($courbe->getId());

        // récuperer le nbre de patient par date d'inclusion
        $nbrepatient = $this->getDoctrine()->getRepository(Patient::class)->findByDateInclusion($courbe->getId());
		//$nbrepatient = $this->getDoctrine()->getRepository(Patient::class)->findBy(['project' => $project]);

        $taille = count($points);

        // 1 ere date
        $data['point'][] = 0;
        $data['abcisse'][] = $inclusion_at->format($formatDate);

        foreach ($points_courbe as $point) {
            // Nombre de patient theorique
            $patient_theorique_count = $point->getY();

            // Intervalle unite - mois ou semaine
            $intervalle = $point->getX();

            // multiplier par 7 (si semaine)
            // multiplier par 30 (si mois)
            $interval_spec = ($intervalle*30);
            if ($is_week) {
                $interval_spec = ($intervalle*7);
            }

            // Date modifiee
            $date_changed = \date($formatDate, \strtotime($inclusion_at->format('Y-m-d').' + '.$interval_spec.' days'));

            $data['point'][] = $patient_theorique_count;
            $data['abcisse'][] = $date_changed;
        }

        $countPatient = 0;
        foreach ($nbrepatient as $nb) {
        	if (null !== $nb['date']) {
				$data['nbrepatient'][] = $countPatient + $nb[1];
				$countPatient+=$nb[1];
			}
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        // Return response
        return $response;
    }

	/**
	 * @Route("/{id}/courbe/new", name="project.list.courbe.new", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('ROLE_PROJECT_SETTINGS_WRITE')")
	 *
	 * @param Request $request
	 * @param CourbeHandler $courbeHandler
	 * @param Project $project
	 * @return Response
	 */
    public function new(Request $request, CourbeHandler $courbeHandler, Project $project): Response
	{
        $courbe = new CourbeSetting();
        $courbe->setProject($project);
        $project->setCourbe($courbe);
        $point = new Point();

        $point->setCourbeSetting($courbe);
        $courbe->addPoint($point);

        if ($courbeHandler->handle($request, $courbe, ['project' => $project])) {
            return $this->redirectToRoute('project.list.courbe.setting', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('courbe/create.html.twig', [
            'form' => $courbeHandler->createView(),
            'project' => $project,
            'edit' => false,
        ]);
    }

    /**
     * @Route("/{project}/courbe/{courbe}/edit", name="project.list.courbe.edit", requirements={"project"="\d+", "courbe"="\d+"})
	 * @ParamConverter("project", options={"id"="project"})
	 * @ParamConverter("courbe", options={"id"="courbe"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('ROLE_PROJECT_SETTINGS_WRITE') and is_granted('ROLE_PROJECT_SETTINGS_WRITE')")
     */
    public function edit(Request $request, CourbeHandler $courbeHandler, Project $project, CourbeSetting $courbe)
    {
        if ($courbeHandler->handle($request, $courbe, ['project' => $project])) {
            return $this->redirectToRoute('project.list.courbe.setting', [
             'id' => $project->getId(),
            ]);
        }

        return $this->render('courbe/create.html.twig', [
            'form' => $courbeHandler->createView(),
            'edit' => true,
        ]);
    }

    /**
     *@Route("/{project}/courbe/{courbe}/show", name="project.list.courbe.show", requirements={"project"="\d+", "courbe"="\d+"})
	 * @ParamConverter("project", options={"id"="project"})
	 * @ParamConverter("courbe", options={"id"="courbe"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project)")
     *
     * @return Response
     */
    public function show(Project $project, CourbeSetting $courbe)
    {
        $courbe = $this->getDoctrine()->getRepository(CourbeSetting::class)->courbeFindByProjectId($project);
        $v = $this->getDoctrine()->getRepository(Point::class)->getPointsByCourbe($courbe);
        $nbrepatient = $this->getDoctrine()->getRepository(Patient::class)->findByDateInclusion($courbe);
        $pointsY = [];
        $nbrepatients = [];

        foreach ($v as $point) {
            $pointsY[] = $point->getY();
        }
        foreach ($nbrepatient as $nb) {
            $nbrepatients[] = $nb;
        }

        return $this->render('courbe/index.html.twig', [
            'id' => $project->getId(),
            'points' => $v,
            'nbrepatient' => $nbrepatient,
            'pointsY' => json_encode($pointsY),
            'nbrepatients' => json_encode($nbrepatients),
            'unit' => json_encode($courbe->getUnit()),
            'inclusionAt' => json_encode($courbe->getStartAt()),
        ]);
    }
}
