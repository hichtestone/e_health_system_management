<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommunicationController
 * @package App\ESM\Controller
 */
class CommunicationController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/projects/{id}/communications", name="project.list.communication", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS') or is_granted('ROLE_COMMUNICATION_READ')")
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $project = $this->entityManager->getRepository(Project::class)->find(['id' => $request->get('id')]);

        return $this->render('communication/index.html.twig', [
            'projectShow' => true,
            'project' => $project,
            'communications' => true,
        ]);
    }
}
