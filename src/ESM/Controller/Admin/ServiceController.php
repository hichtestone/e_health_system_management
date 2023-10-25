<?php

namespace App\ESM\Controller\Admin;

use App\ESM\Entity\Institution;
use App\ESM\Entity\Service;
use App\ESM\FormHandler\ServiceHandler;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ServiceController
 * @package App\ESM\Controller\Admin
 */
class ServiceController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

	/**
	 * @Route("/admin/institutions/{id}/services/new", name="admin.service.new", requirements={"id"="\d+"})
	 * @Security("is_granted('INSTITUTION_EDIT', institution)")
	 *
	 * @param Request $request
	 * @param ServiceHandler $formHandler
	 * @param Institution $institution
	 * @param RouterInterface $router
	 * @return Response
	 */
    public function new(Request $request, ServiceHandler $formHandler, Institution $institution, RouterInterface $router): Response
	{
        $service = new Service();
        $service->setInstitution($institution);

        if ($formHandler->handle($request, $service, ['institution' => $institution])) {
            return $this->redirectToRoute('admin.institution.show', ['id' => $institution->getId()]);
        }

        if (count($formHandler->getForm()->getErrors())) {
            $request->getSession()->getFlashBag()->add('danger', 'Ce service existe déjà');

            return $this->redirectToRoute('admin.institution.show', ['id' => $institution->getId()]);
        }

        return $this->json([
            'status' => 1,
            'html' => $this->renderView('admin/service/create.html.twig', [
                'form' 	 => $formHandler->createView(),
                'action' => 'create',
                'url' 	 => $router->generate('admin.service.new', ['id' => $institution->getId()]),
            ]),
        ]);
    }

    /**
     * @Route("/admin/institutions/{id}/services/{idService}/edit", name="admin.service.edit", requirements={"id"="\d+", "idService"="\d+"})
     * @ParamConverter("service", options={"id"="idService"})
     * @Security("is_granted('SERVICE_EDIT', service)")
     *
     * @return Response
     */
    public function edit(Request $request, ServiceHandler $formHandler, Institution $institution, Service $service, RouterInterface $router)
    {
        if ($formHandler->handle($request, $service, ['institution' => $institution])) {
            return $this->redirectToRoute('admin.institution.show', ['id' => $institution->getId()]);
        }

        if (count($formHandler->getForm()->getErrors())) {
            $request->getSession()->getFlashBag()->add('danger', 'Ce service existe déjà');

            return $this->redirectToRoute('admin.institution.show', ['id' => $institution->getId()]);
        }

        return $this->json([
            'status' => 1,
            'html' => $this->renderView('admin/service/create.html.twig', [
                'form' => $formHandler->createView(),
                'action' => 'edit',
                'url' => $router->generate('admin.service.edit', ['id' => $institution->getId(), 'idService' => $service->getId()]),
            ]),
        ]);
    }

    /**
     * @Route("/admin/institutions/{id}/services/{idService}/archive", name="admin.service.archive", requirements={"id"="\d+", "idService"="\d+"})
     * @ParamConverter("service", options={"id"="idService"})
     * @Security("is_granted('SERVICE_ARCHIVE', service)")
     *
     * @param Institution $institution
     * @param Service $service
     * @return Response
     */
    public function archive(Institution $institution, Service $service)
    {
        $service->setDeletedAt(new \DateTime());
        $this->entityManager->persist($service);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin.institution.show', [
            'id' => $institution->getId(),
        ]);
    }

    /**
     * @Route("/admin/institutions/{id}/services/{idService}/restore", name="admin.service.restore", requirements={"id"="\d+", "idService"="\d+"})
     * @ParamConverter("service", options={"id"="idService"})
     * @Security("is_granted('SERVICE_RESTORE', service)")
     *
     * @param Institution $institution
     * @param Service $service
     * @return Response
     */
    public function restore(Institution $institution, Service $service)
    {
        $service->setDeletedAt(null);
        $this->entityManager->persist($service);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin.institution.show', [
            'id' => $institution->getId(),
        ]);
    }
}
