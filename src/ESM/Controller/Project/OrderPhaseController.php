<?php

namespace App\ESM\Controller\Project;

use App\ESM\Entity\PhaseSetting;
use App\ESM\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OrderPhaseController
 * @package App\ESM\Controller\Project
 */
class OrderPhaseController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/projects/{id}/phase-settings/phase", name="project.phase.setting.order", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('PHASESETTING_LIST')")
     */
    public function index(Project $project): Response
    {
        $phases = $this->entityManager->getRepository(PhaseSetting::class)
            ->findBy(['project' => $project, 'deletedAt' => null], ['position' => 'ASC']);

        return $this->json(array_map(function ($phase) {
            return [
                'id' => $phase->getId(),
                'name' => $phase->getLabel(),
                'order' => $phase->getOrdre(),
                'position' => $phase->getPosition(),
            ];
        }, $phases));
    }

    /**
     * @Route("/projects/{id}/phase-settings/order/edit", name="project.phase.setting.order.edit", methods="PUT", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('PHASESETTING_CREATE')")
     */
    public function edit(Project $project, Request $request): Response
    {
        $status = 0;
        $msg = 'Error';

        $data = json_decode($request->getContent(), true);
        if ($data) {
            foreach ($data['data'] as $phase) {
                $entity = $this->entityManager->getRepository(PhaseSetting::class)->find($phase['id']);
                $entity->setPosition($phase['position']);

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
