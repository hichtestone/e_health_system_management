<?php

namespace App\ESM\Controller\Project;

use App\ESM\Entity\Project;
use App\ESM\Entity\Visit;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderVisitController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/projects/{id}/visit-settings/order", name="project.visit.setting.order", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('VISITSETTING_CREATE')")
     */
    public function index(Project $project): Response
    {
        $visits = $this->entityManager->getRepository(Visit::class)
            ->findBy(['project' => $project, 'deletedAt' => null], ['position' => 'ASC']);

        return $this->json(array_map(function ($visit) {
            return [
                'id' => $visit->getId(),
                'name' => $visit->getShort(),
                'order' => $visit->getOrdre(),
                'position' => $visit->getPosition(),
            ];
        }, $visits));
    }

    /**
     * @Route("/projects/{id}/visit-settings/order/edit", name="project.visit.setting.order.edit", methods="PUT", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('VISITSETTING_CREATE')")
     */
    public function edit(Project $project, Request $request): Response
    {
        $status = 0;
        $msg = 'Error';

        $data = json_decode($request->getContent(), true);
        if ($data) {
            foreach ($data['data'] as $visit) {
                $entity = $this->entityManager->getRepository(Visit::class)->find($visit['id']);
                $entity->setPosition($visit['position']);

                $this->entityManager->persist($entity);
                $this->entityManager->flush();
            }
        }

        $status = 1;
        $msg = 'Ok';

        return $this->json([
            'status' => $status,
            'msg' => $msg,
        ]);
    }
}
