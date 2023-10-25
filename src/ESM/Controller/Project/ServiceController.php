<?php

namespace App\ESM\Controller\Project;

use App\ESM\Entity\Project;
use App\ESM\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ServiceController
 * @package App\ESM\Controller\Project
 */
class ServiceController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/projects/{id}/service/{idService}", name="project.service.show", methods="GET", requirements={"id"="\d+", "idService"="\d+"})
     * @ParamConverter("service", options={"id"="idService"})
     * @Security("is_granted('PROJECT_ACCESS', project)")
     */
    public function show(Project $project, Service $service): Response
    {
        return $this->render('project/service/show.html.twig', [
            'service' => $service,
        ]);
    }
}
