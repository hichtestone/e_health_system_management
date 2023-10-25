<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Date;
use App\ESM\Entity\ProjectDatabaseFreeze;
use App\ESM\FormHandler\DatabaseFreezeHandler;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ProjectDatabaseFreezeController
 * @package App\ESM\Controller
 */
class ProjectDatabaseFreezeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/project/dates/{id}/databsefreeze/new", name="project.dates.databasefreeze.new", requirements={"id"="\d+"})
     * @Security("is_granted('DATE_EDIT', projectDates)")
     */
    public function new(Request $request, DatabaseFreezeHandler $formHandler, Date $projectDates, RouterInterface $router): Response
    {
        $databaseFreeze = new ProjectDatabaseFreeze();
        $databaseFreeze->setProjectDate($projectDates);

        if ($formHandler->handle($request, $databaseFreeze)) {
            return $this->redirectToRoute('project.list.dates', ['id' => $projectDates->getProject()->getId()]);
        }

        return $this->json([
            'status' => 1,
            'html' => $this->renderView('project/databasefreeze/create.html.twig', [
                'form' => $formHandler->createView(),
                'action' => 'create',
                'url' => $router->generate('project.dates.databasefreeze.new', ['id' => $projectDates->getId()]),
            ]),
        ]);
    }

    /**
     * @Route("/project/dates/{id}/databsefreeze/{idDatabasefreeze}/edit", name="project.dates.databasefreeze.edit", requirements={"id"="\d+", "idDatabasefreeze"="\d+"})
     * @ParamConverter("databaseFreeze", options={"id"="idDatabasefreeze"})
     * @Security("is_granted('DATABASEFREEZE_EDIT', databaseFreeze)")
     */
    public function edit(Request $request, DatabaseFreezeHandler $formHandler, Date $projectDates, ProjectDatabaseFreeze $databaseFreeze, RouterInterface $router): Response
    {
        if ($formHandler->handle($request, $databaseFreeze)) {
            return $this->redirectToRoute('project.list.dates', ['id' => $projectDates->getProject()->getId()]);
        }

        return $this->json([
            'status' => 1,
            'html' => $this->renderView('project/databasefreeze/create.html.twig', [
                'form' => $formHandler->createView(),
                'action' => 'edit',
                'url' => $router->generate('project.dates.databasefreeze.edit', ['id' => $projectDates->getId(), 'idDatabasefreeze' => $databaseFreeze->getId()]),
            ]),
        ]);
    }

    /**
     * @Route("/project/dates/{id}/databsefreeze/{idDatabasefreeze}/archive", name="project.dates.databasefreeze.archive", requirements={"id"="\d+", "idDatabasefreeze"="\d+"})
     * @ParamConverter("databaseFreeze", options={"id"="idDatabasefreeze"})
     * @Security("is_granted('DATABASEFREEZE_ARCHIVE', databaseFreeze)")
     */
    public function archive(Date $projectDates, ProjectDatabaseFreeze $databaseFreeze): Response
    {
        $databaseFreeze->setDeletedAt(new \DateTime());
        $this->entityManager->persist($databaseFreeze);
        $this->entityManager->flush();

        return $this->redirectToRoute('project.list.dates', [
            'id' => $projectDates->getProject()->getId(),
        ]);
    }

    /**
     * @Route("/project/dates/{id}/databsefreeze/{idDatabasefreeze}/restore", name="project.dates.databasefreeze.restore", requirements={"id"="\d+", "idDatabasefreeze"="\d+"})
     * @ParamConverter("databaseFreeze", options={"id"="idDatabasefreeze"})
     * @Security("is_granted('DATABASEFREEZE_RESTORE', databaseFreeze)")
     */
    public function restore(Date $projectDates, ProjectDatabaseFreeze $databaseFreeze): Response
    {
        $databaseFreeze->setDeletedAt(null);
        $this->entityManager->persist($databaseFreeze);
        $this->entityManager->flush();

        return $this->redirectToRoute('project.list.dates', [
            'id' => $projectDates->getProject()->getId(),
        ]);
    }
}
