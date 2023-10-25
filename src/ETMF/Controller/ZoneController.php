<?php

namespace App\ETMF\Controller;

use App\ESM\Service\ListGen\ListGenFactory;
use App\ETMF\Entity\Zone;
use App\ETMF\Form\ZoneType;
use App\ETMF\ListGen\ZoneList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/etmf/structure/zone")
 */
class ZoneController extends AbstractController
{
	/**
	 * @Route("/", name="app.etmf.zone")
	 * @Security("is_granted('ZONE_LIST')")
	 * @param ListGenFactory $lgm
	 * @return Response
	 */
    public function index(ListGenFactory $lgm): Response
    {
		$list = $lgm->getListGen(ZoneList::class);

		return $this->render('ETMF/admin/zone/index.html.twig', [
			'activeMenu' => 'etmf-artefact',
			'list' 		 => $list->getList(),
		]);
	}

	/**
	 * @Route("/ajax", name="app.etmf.zone.ajax")
	 * @Security("is_granted('ZONE_LIST')")
	 * @param Request $request
	 * @param ListGenFactory $lgm
	 * @return mixed
	 */
	public function indexAjax(Request $request, ListGenFactory $lgm)
	{
		$list = $lgm->getListGen(ZoneList::class);
		$list = $list->getList();
		$list->setRequestParams($request->query);

		return $list->generateResponse();
	}

	/**
	 * @Route("/new", name="app.etmf.zone.new")
	 * @Security("is_granted('ZONE_CREATE')")
	 * @param Request $request
	 * @return RedirectResponse|Response
	 * @throws \Exception
	 */
	public function new(Request $request)
	{
		$zone = new Zone();

		$form = $this->createForm(ZoneType::class, $zone, []);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			try {

				$em = $this->getDoctrine()->getManager();
				$em->persist($zone);
				$em->flush();

			} catch (\Exception $exception) {

				throw new Exception('impossible to persist zone !');
			}

			return $this->redirectToRoute('app.etmf.zone', []);
		}

		return $this->render('ETMF/admin/zone/create.html.twig', [
			'form' => $form->createView(),
			'edit' => false,
		]);
	}

	/**
	 * @Route("/{zoneID}/edit", name="app.etmf.zone.edit", requirements={"zoneID"="\d+"})
	 * @ParamConverter("zone", options={"id"="zoneID"})
	 * @Security("is_granted('ZONE_EDIT', zone)")
	 * @param Request $request
	 * @param Zone $zone
	 * @return Response
	 * @throws \Exception
	 */
	public function edit(Request $request, Zone $zone): Response
	{
		$form = $this->createForm(ZoneType::class, $zone, []);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			try {

				$em = $this->getDoctrine()->getManager();
				$em->persist($zone);
				$em->flush();

			} catch (\Exception $exception) {

				throw new Exception('impossible to persist zone !');
			}

			return $this->redirectToRoute('app.etmf.zone', []);
		}

		return $this->render('ETMF/admin/zone/create.html.twig', [
			'form' => $form->createView(),
			'edit' => true,
		]);
	}

	/**
	 * @Route("/{zoneID}/archive", name="app.etmf.zone.archive", requirements={"zoneID"="\d+"})
	 * @ParamConverter("zone", options={"id"="zoneID"})
	 * @Security("is_granted('ZONE_ARCHIVE', zone)")
	 * @param Zone $zone
	 * @return Response
	 */
	public function archive(Zone $zone): Response
	{
		if ($zone->hasDocuments()) {

			$this->addFlash('warning', 'ARCHIVAGE IMPOSSIBLE : cette zone possède déjà des documents!');

		} else {

			$entityManager = $this->getDoctrine()->getManager();
			$zone->setDeletedAt(new \DateTime());

			$entityManager->persist($zone);
			$entityManager->flush();
		}

		return $this->redirectToRoute('app.etmf.zone', []);
	}

	/**
	 * @Route("/{zoneID}/restore", name="app.etmf.zone.restore", requirements={"zoneID"="\d+"})
	 * @ParamConverter("zone", options={"id"="zoneID"})
	 * @Security("is_granted('ZONE_RESTORE', zone)")
	 * @param Zone $zone
	 * @return Response
	 */
	public function restore(Zone $zone): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$zone->setDeletedAt(null);

		$entityManager->persist($zone);
		$entityManager->flush();

		return $this->redirectToRoute('app.etmf.zone', []);
	}
}
