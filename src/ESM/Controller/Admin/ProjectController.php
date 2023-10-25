<?php

namespace App\ESM\Controller\Admin;

use App\ESM\Entity\AuditTrail\ProjectAuditTrail;
use App\ESM\Entity\Date;
use App\ESM\Entity\DropdownList\CrfType;
use App\ESM\Entity\DropdownList\ProjectStatus;
use App\ESM\Entity\DropdownList\TrailTreatment;
use App\ESM\Entity\Drug;
use App\ESM\Entity\PatientVariable;
use App\ESM\Entity\Project;
use App\ESM\Entity\ProjectTrailTreatment;
use App\ESM\Entity\Rule;
use App\ESM\Entity\User;
use App\ESM\Entity\VariableType;
use App\ESM\Entity\VersionDocumentTransverse;
use App\ESM\FormHandler\ProjectHandler;
use App\ESM\ListGen\Admin\ProjectList;
use App\ESM\Notification\ClosedNotification;
use App\ESM\Notification\CreationProjectNotification;
use App\ESM\Repository\DrugRepository;
use App\ESM\Repository\ProfileRepository;
use App\ESM\Repository\ProjectTrailTreatmentRepository;
use App\ESM\Repository\TrailTreatmentRepository;
use App\ESM\Repository\UserRepository;
use App\ESM\Repository\VersionDocumentTransverseRepository;
use App\ESM\Service\ListGen\ListGenFactory;
use DateTime;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\ESM\Events\DateEvent;

/**
 * @Route("/admin/projects")
 */
class ProjectController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;


    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/", name="admin.projects.index")
     * @Security("is_granted('PROJECT_LIST')")
     */
    public function index(Request $request, ListGenFactory $lgm): Response
    {
        $list = $lgm->getListGen(ProjectList::class);

        $user = $this->entityManager->getRepository(User::class)->find($this->getUser()->getId());

        if (null !== $user) {
            return $this->render('admin/project/index.html.twig', [
                'list' => $list->getList($user->getId(), $request->getLocale()),
            ]);
        }

        return $this->render('admin/project/index.html.twig');
    }

    /**
     * @Route("/ajax/projects", name="admin.project.index.ajax")
     * @Security("is_granted('PROJECT_LIST')")
     *
     * @return Response
     */
    public function indexAjax(Request $request, ListGenFactory $lgm)
    {
        $user = $this->entityManager->getRepository(User::class)->find($this->getUser()->getId());

        // listgen handle request
        $list = $lgm->getListGen(ProjectList::class);
        $list = $list->getList($user->getId(), $request->getLocale());
        $list->setRequestParams($request->query);

        // json response
        return $list->generateResponse();
    }

	/**
	 * @Route("/new", name="admin.project.new")
	 * @Security("is_granted('PROJECT_CREATE')")
	 *
	 * @param Request $request
	 * @param ProjectHandler $projectHandler
	 * @param CreationProjectNotification $creationProjectNotification
	 * @param ProfileRepository $profileRepository
	 * @param UserRepository $userRepository
	 * @param TrailTreatmentRepository $trailTreatmentRepository
	 * @param DrugRepository $drugRepository
	 * @param VersionDocumentTransverseRepository $versionDocumentTransverseRepository
	 * @return Response
	 * @throws Exception
	 * @throws \Doctrine\DBAL\Exception
	 */
    public function new(Request $request, ProjectHandler $projectHandler, CreationProjectNotification $creationProjectNotification, ProfileRepository $profileRepository, UserRepository $userRepository, TrailTreatmentRepository $trailTreatmentRepository, DrugRepository $drugRepository, VersionDocumentTransverseRepository $versionDocumentTransverseRepository): Response
	{
        $variableType = $this->entityManager->getRepository(VariableType::class)->findOneBy(['label' => 'date']);

        $crfType = $this->getDoctrine()->getRepository(CrfType::class)->findOneBy(['label' => 'Ennov' || 'Clinfile']);
        $project = new Project();
        $project->setAppToken(uniqid('', true));
        $project->setCrfType($crfType);

        if ($projectHandler->handle($request, $project)) {

            // TODO event subscriber with audit trail
            $date = new Date();
            $date->setProject($project);
            $this->entityManager->persist($date);

            // TODO event subscriber
            $rule = new Rule();
            $rule->setProject($project);
            $this->entityManager->persist($rule);

            $destinations = [];

            foreach ($profileRepository->findAllUsersCanCloseDemand('ROLE_PROJECT_WRITE') as $profile => $name) {
                foreach ($userRepository->getListUsersCloseDemandByEmail($name['name']) as $user => $email) {
					if (!in_array($email['email'], $destinations, true)) {
						$destinations[] = $email['email'];
					}
                }
            }

			foreach ($destinations as $destination) {
				$creationProjectNotification->notify($this->getUser(), $request, 'Création/Edition projet', 'project_confirmation', $destination, ['project' => $project]);
			}

            $patientVariableData = ['Date de signature du consentement', 'Date d\'inclusion'];

            foreach ($patientVariableData as $patientVariableDatum) {

                $patientVariable = new PatientVariable();
                $patientVariable->setProject($project);
                $patientVariable->setVariableType($variableType);
                $patientVariable->setLabel($patientVariableDatum);
                $patientVariable->setIsVariable(true);
                $patientVariable->setHasPatient(true);
                $patientVariable->setSys(true);

                $this->entityManager->persist($patientVariable);
            }

            // Add drug to project
            $trail_treatments 	= null !== $request->get('trail_treatment') ? \array_filter($request->get('trail_treatment')) : [];
            $drugs 				= null !== $request->get('drug') 			 ? \array_filter($request->get('drug')) 			: [];
            $versions_bi 		= null !== $request->get('version_bi') 	 ? \array_filter($request->get('version_bi')) 		: [];
            $versions_rcp 		= null !== $request->get('version_rcp') 	 ? \array_filter($request->get('version_rcp')) 	: [];

            if (isset($trail_treatments)) {
                foreach ($trail_treatments as $trail_treatment_id) {

                    $trail_treatment_entity = $this->getDoctrine()->getRepository(TrailTreatment::class)->find($trail_treatment_id);

                    $drug_id = $this->getDrugId($trail_treatment_id, $drugs);
                    $drug_entity = $this->getDoctrine()->getRepository(Drug::class)->find($drug_id);

                    $version_bi_id = $this->getVersionId($trail_treatment_id, $versions_bi);
                    $version_bi_entity = $this->getDoctrine()->getRepository(VersionDocumentTransverse::class)->find($version_bi_id);

                    $version_rcp_id = $this->getVersionId($trail_treatment_id, $versions_rcp);
                    $version_rcp_entity = $this->getDoctrine()->getRepository(VersionDocumentTransverse::class)->find($version_rcp_id);

                    $project_trailTreatment = new ProjectTrailTreatment();
                    $project_trailTreatment->setProject($project);
                    $project_trailTreatment->setTrailTreatment($trail_treatment_entity);
                    $project_trailTreatment->setDrug($drug_entity);
                    $project_trailTreatment->setVersionBi($version_bi_entity);
                    $project_trailTreatment->setVersionRcp($version_rcp_entity);

                    $this->entityManager->persist($project_trailTreatment);
                }
            }

            $this->entityManager->flush();

            return $this->redirectToRoute('admin.projects.index');
        }

        return $this->render('admin/project/create.html.twig', [
        	'form' => $projectHandler->createView(),
            'action' => 'create',

            // Traitements
            'trail_treatment_list' => $trailTreatmentRepository->findAll(),

            // Medicaments non archives avec des documents transverses
            'drugs_list' => $drugRepository->findAllHasDoc(),

            // Versions des documents transverses de type BI dont les medicaments sont non archives
            'version_bi_list' => $versionDocumentTransverseRepository->findByTypeAndDoc('BI'),

            // Versions des documents transverses de type RCP dont les medicaments sont non archives
            'version_rcp_list' => $versionDocumentTransverseRepository->findByTypeAndDoc('RCP'),
        ]);
    }

	/**
	 * @Route("/{id}/show", name="admin.project.show", methods="GET", requirements={"id"="\d+"})
	 * @Security("is_granted('PROJECT_SHOW', project) or is_granted('ROLE_PROJECT_CLOSE')")
	 *
	 * @param Project $project
	 * @param ProjectTrailTreatmentRepository $projectTrailTreatmentRepository
	 * @return Response
	 */
    public function show(Project $project, ProjectTrailTreatmentRepository $projectTrailTreatmentRepository): Response
	{
        $form = null;

        if ($this->isGranted('USER_EDIT', $project)) {
            // create form
            $form = $this->createForm(Project::class, $project, [
                 'action' => $this->generateUrl('admin.project.edit', ['id' => $project->getId()]),
                 'form_type' => 'edit',
             ]);
        }

        return $this->render('admin/project/show.html.twig', [
             'project' 			=> $project,
             'form' 			=> null !== $form ? $form->createView() : null,
             'action' 			=> 'show',
             'trail_treatments' => $projectTrailTreatmentRepository->findBy(['project' => $project]),
         ]);
    }

    /**
     * @Route("/{id}/edit", name="admin.project.edit", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_EDIT', project)")
     *
     * @return RedirectResponse|Response
     */
    public function edit(ProjectHandler $projectHandler, Request $request, Project $project)
    {
        $options = [
            'action' => $this->generateUrl('admin.project.edit', ['id' => $project->getId()]),
            'statusDisabled' => null !== $project->getCloseDemandedAt(),
        ];

        if ($projectHandler->handle($request, $project, $options)) {
            return $this->redirectToRoute('admin.project.show', ['id' => $project->getId()]);
        }

        return $this->render('admin/project/create.html.twig', [
            'form' => $projectHandler->createView(),
            'action' => 'edit',

            // Traitements
            'trail_treatment_list' => [],

            // Medicaments non archives avec des documents transverses
            'drugs_list' => [],

            // Versions des documents transverses de type BI dont les medicaments sont non archives
            'version_bi_list' => [],

            // Versions des documents transverses de type RCP dont les medicaments sont non archives
            'version_rcp_list' => [],
        ]);
    }

	/**
	 *
	 * @Route("/closeDemand/{id}", name="admin.project.closeDemand", methods="GET", requirements={"id"="\d+"})
	 * @Security("is_granted('PROJECT_CLOSE_DEMAND', project)")
	 *
	 * @param Project $project
	 * @param Request $request
	 * @param ProfileRepository $profileRepository
	 * @param UserRepository $userRepository
	 * @param ClosedNotification $closeDemandNotification
	 * @return Response
	 * @throws Exception
	 * @throws \Doctrine\DBAL\Exception
	 */
    public function closeDemand(Project $project, Request $request, ProfileRepository $profileRepository, UserRepository $userRepository, ClosedNotification $closeDemandNotification): Response
	{
        $projectStatus = $this->getDoctrine()->getRepository(ProjectStatus::class)->find(5);

        // mettre à jour
        $project->setClosedDemandBy($this->getUser());
        $project->setCloseDemandedAt(new DateTime());
        $project->setProjectStatus($projectStatus);

		$destinations = [];

        foreach ($profileRepository->findAllUsersCanCloseDemand('ROLE_PROJECT_CLOSE_DEMAND') as $profile => $name) {
            foreach ($userRepository->getListUsersCloseDemandByEmail($name['name']) as $user => $email) {
            	if (!in_array($email['email'], $destinations)) {
            		$destinations[] = $email['email'];
				}
            }
        }

		foreach ($destinations as $destination) {
			$closeDemandNotification->notify($this->getUser(), $request, 'Demande de clôture projet '.$project->getName(), 'closeDemand', $destination, ['project' => $project, 'user' => $this->getUser()]);
		}

		$this->entityManager->persist($project);
		$this->entityManager->flush();

        return $this->redirectToRoute('admin.project.show', [
            'id' => $project->getId(),
            'project' => $project,
        ]);
    }

	/**
	 * @Route("/close/{id}", name="admin.project.close", methods="GET", requirements={"id"="\d+"})
	 * @Security("is_granted('PROJECT_CLOSE', project)")
	 *
	 * @param Project $project
	 * @param Request $request
	 * @param ProfileRepository $profileRepository
	 * @param UserRepository $userRepository
	 * @param ClosedNotification $closedNotification
	 * @return Response
	 * @throws Exception
	 * @throws \Doctrine\DBAL\Exception
	 * @return Response
	 */
    public function close(Project $project, Request $request, ProfileRepository $profileRepository, UserRepository $userRepository, ClosedNotification $closedNotification): Response
	{
        $projectStatus = $this->getDoctrine()->getRepository(ProjectStatus::class)->find(6);

        // mettre à jour
        $project->setClosedAt(new DateTime());
        $project->setProjectStatus($projectStatus);

		$destinations = [];

        foreach ($profileRepository->findAllUsersCanCloseDemand('ROLE_PROJECT_CLOSE_DEMAND') as $profile => $name) {
            foreach ($userRepository->getListUsersCloseDemandByEmail($name['name']) as $user => $email) {
				if (!in_array($email['email'], $destinations, true)) {
					$destinations[] = $email['email'];
				}
            }
        }

		foreach ($destinations as $destination) {
			$closedNotification->notify($this->getUser(), $request, 'Acceptation clôture projet '.$project->getName(), 'closed', $destination, ['project' => $project, 'user' => $this->getUser()]);
		}

		$this->entityManager->persist($project);
		$this->entityManager->flush();

        return $this->redirectToRoute('admin.project.show', [
            'id' => $project->getId(),
        ]);
    }

	/**
	 * @Route("/close-not/{id}", name="admin.project.close_not", methods="GET", requirements={"id"="\d+"})
	 * @Security("is_granted('PROJECT_CLOSE', project)")
	 *
	 * @param Project $project
	 * @param Request $request
	 * @param ProfileRepository $profileRepository
	 * @param UserRepository $userRepository
	 * @param ClosedNotification $closedNotification
	 * @return Response
	 * @throws Exception
	 * @throws \Doctrine\DBAL\Exception
	 */
    public function closeNot(Project $project, Request $request, ProfileRepository $profileRepository, UserRepository $userRepository, ClosedNotification $closedNotification): Response
	{
        // mettre à jour
        $projectStatus = $this->getLastProjectStatus($project);
        $project->setClosedDemandBy(null);
        $project->setCloseDemandedAt(null);
        $project->setProjectStatus($projectStatus);

		$destinations = [];

        foreach ($profileRepository->findAllUsersCanCloseDemand('ROLE_PROJECT_CLOSE_DEMAND') as $profile => $name) {
            foreach ($userRepository->getListUsersCloseDemandByEmail($name['name']) as $user => $email) {
				if (!in_array($email['email'], $destinations)) {
					$destinations[] = $email['email'];
				}
            }
        }

		foreach ($destinations as $destination) {
			$closedNotification->notify($this->getUser(), $request, 'Refus clôture projet '.$project->getName(), 'annulerClotureProjet', $destination, ['project' => $project, 'user' => $this->getUser()]);
		}

		$this->entityManager->persist($project);
		$this->entityManager->flush();

        return $this->redirectToRoute('admin.project.show', [
            'id' => $project->getId(),
        ]);
    }

	/**
	 * @Route("/{id}/open", name="admin.project.open", methods="GET", requirements={"id"="\d+"})
	 * @Security("is_granted('PROJECT_OPEN', project)")
	 *
	 * @param Project $project
	 * @param Request $request
	 * @param ProfileRepository $profileRepository
	 * @param UserRepository $userRepository
	 * @param ClosedNotification $closedNotification
	 * @return Response
	 * @throws Exception
	 * @throws \Doctrine\DBAL\Exception
	 */
    public function open(Project $project, Request $request, ProfileRepository $profileRepository, UserRepository $userRepository, ClosedNotification $closedNotification): Response
	{
        // mettre à jour
        $projectStatus = $this->getLastProjectStatus($project);
        $project->setClosedAt(null);
        $project->setClosedDemandBy(null);
        $project->setCloseDemandedAt(null);
        $project->setProjectStatus($projectStatus);

		$destinations = [];


        foreach ($profileRepository->findAllUsersCanCloseDemand('ROLE_PROJECT_CLOSE_DEMAND') as $profile => $name) {
            foreach ($userRepository->getListUsersCloseDemandByEmail($name['name']) as $user => $email) {
				if (!in_array($email['email'], $destinations)) {
					$destinations[] = $email['email'];
				}
            }
        }

		foreach ($destinations as $destination) {
			$closedNotification->notify($this->getUser(), $request, 'Annulation clôture', 'canceled', $destination, ['project' => $project, 'user' => $this->getUser()]);
		}

		$this->entityManager->persist($project);
		$this->entityManager->flush();


        return $this->redirectToRoute('admin.project.show', [
            'id' => $project->getId(),
        ]);
    }

    /**
     * @return ProjectStatus|object|null
     */
    private function getLastProjectStatus(Project $project)
    {
        $at = $this->getDoctrine()->getRepository(ProjectAuditTrail::class)->findBy(['entity' => $project], ['date' => 'DESC']);

        foreach ($at as $auditTrail) {
            $details = $auditTrail->getDetails();

            if (array_key_exists('projectStatus', $details)) {
                preg_match('/.+\\((\d+)\\)/', $details['projectStatus'][0], $matches);

                if (2 === count($matches)) {
                    $id = $matches[1];

                    return $this->getDoctrine()->getRepository(ProjectStatus::class)->find($id);
                }
            }
        }

        return $this->getDoctrine()->getRepository(ProjectStatus::class)->find(1);
    }

	/**
	 * @param int $trail_treatment_id
	 * @param array $drugs
	 * @return int
	 */
    private function getDrugId(int $trail_treatment_id, array $drugs): int
    {
        // format : "t:1_d:4"
        foreach ($drugs as $drug) {
            $parts = explode('_', $drug);
            $part_trailtreatment = str_replace('t:', '', $parts[0]); // t:1
            $part_drug = str_replace('d:', '', $parts[1]); // d:1

            if ((int) $part_trailtreatment === $trail_treatment_id) {
                return (int) $part_drug;
            }
        }

        return 0;
    }

	/**
	 * @param int $trail_treatment_id
	 * @param array $versions
	 * @return int
	 */
    private function getVersionId(int $trail_treatment_id, array $versions): int
    {
        // format : "t:2_d:1_v:14"
        foreach ($versions as $version) {
            $parts = explode('_', $version);
            $part_trailtreatment = str_replace('t:', '', $parts[0]); // t:1
            $part_version = str_replace('v:', '', $parts[2]); // v:1

            if ((int) $part_trailtreatment === $trail_treatment_id) {
                return (int) $part_version;
            }
        }

        return 0;
    }

	/**
	 * @Route("/{id}/new-trail-treatment", name="admin.project.new_trail_treatment", requirements={"id"="\d+"})
	 * @Security("is_granted('PROJECT_EDIT', project)")
	 *
	 * @param Request $request
	 * @param Project $project
	 * @param TrailTreatmentRepository $trailTreatmentRepository
	 * @param DrugRepository $drugRepository
	 * @param VersionDocumentTransverseRepository $versionDocumentTransverseRepository
	 * @return Response
	 */
    public function create_trail_treatment(Request $request, Project $project, TrailTreatmentRepository $trailTreatmentRepository, DrugRepository $drugRepository, VersionDocumentTransverseRepository $versionDocumentTransverseRepository): Response
	{
        if (null !== $request->get('trail_treatment')) {
            $entityManager = $this->getDoctrine()->getManager();

            // Add drug to project
            $trail_treatments = \array_filter($request->get('trail_treatment'));
            $drugs = \array_filter($request->get('drug'));
            $versions_bi = (null !== $request->get('version_bi')) ? \array_filter($request->get('version_bi')) : [];
            $versions_rcp = (null !== $request->get('version_rcp')) ? \array_filter($request->get('version_rcp')) : [];
			//dd($trail_treatments);
            foreach ($trail_treatments as $trail_treatment_id) {
                /** @var TrailTreatment $trail_treatment_entity */
                $trail_treatment_entity = $this->getDoctrine()->getRepository(TrailTreatment::class)->find($trail_treatment_id);

                $drug_id = $this->getDrugId($trail_treatment_id, $drugs);
                /** @var Drug $drug_entity */
                $drug_entity = $this->getDoctrine()->getRepository(Drug::class)->find($drug_id);

                $version_bi_id = $this->getVersionId($trail_treatment_id, $versions_bi);
                /** @var VersionDocumentTransverse $version_bi_entity */
                $version_bi_entity = $this->getDoctrine()->getRepository(VersionDocumentTransverse::class)->find($version_bi_id);

                $version_rcp_id = $this->getVersionId($trail_treatment_id, $versions_rcp);
                /** @var VersionDocumentTransverse $version_rcp_entity */
                $version_rcp_entity = $this->getDoctrine()->getRepository(VersionDocumentTransverse::class)->find($version_rcp_id);

                // save
                $project_trailTreatment = new ProjectTrailTreatment();
                $project_trailTreatment->setProject($project);
                $project_trailTreatment->setTrailTreatment($trail_treatment_entity);
                $project_trailTreatment->setDrug($drug_entity);
                $project_trailTreatment->setVersionBi($version_bi_entity);
                $project_trailTreatment->setVersionRcp($version_rcp_entity);

                $entityManager->persist($project_trailTreatment);

                // project_drug
				if ($drug_entity instanceof Drug) {
					$project->addDrug($drug_entity);
					$entityManager->persist($project);
				}
            }

            $entityManager->flush();

            return $this->redirectToRoute('admin.project.show', ['id' => $project->getId()]);
        }

        return $this->render('admin/project/create_trail_treatment.html.twig', [
            // Traitements
            'trail_treatment_list' => $trailTreatmentRepository->findAll(),

            // Medicaments non archives avec des documents transverses
            'drugs_list' => $drugRepository->findAllHasDoc(),

            // Versions des documents transverses de type BI dont les medicaments sont non archives
            'version_bi_list' => $versionDocumentTransverseRepository->findByTypeAndDoc('BI'),

            // Versions des documents transverses de type RCP dont les medicaments sont non archives
            'version_rcp_list' => $versionDocumentTransverseRepository->findByTypeAndDoc('RCP'),
        ]);
    }
}
