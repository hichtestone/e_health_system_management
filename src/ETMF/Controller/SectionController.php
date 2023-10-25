<?php

namespace App\ETMF\Controller;

use App\ESM\Service\ListGen\ListGenFactory;
use App\ETMF\Entity\Section;
use App\ETMF\FormHandler\SectionHandler;
use App\ETMF\ListGen\SectionList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/etmf/structure/section")
 */
class SectionController extends AbstractController
{
	/**
	 * @Route("/", name="app.etmf.section")
	 * @Security("is_granted('SECTION_LIST')")
	 * @param ListGenFactory $lgm
	 * @return Response
	 */
    public function index(ListGenFactory $lgm): Response
    {
		$list = $lgm->getListGen(SectionList::class);

		return $this->render('ETMF/admin/section/index.html.twig', [
			'activeMenu' => 'etmf-artefact',
			'list' 		 => $list->getList(),
		]);
	}

	/**
	 * @Route("/ajax", name="app.etmf.section.ajax")
	 * @Security("is_granted('SECTION_LIST')")
	 * @param Request $request
	 * @param ListGenFactory $lgm
	 * @return mixed
	 */
	public function indexAjax(Request $request, ListGenFactory $lgm)
	{
		$list = $lgm->getListGen(SectionList::class);
		$list = $list->getList();
		$list->setRequestParams($request->query);

		return $list->generateResponse();
	}

	/**
	 * @Route("/new", name="app.etmf.section.new")
	 * @Security("is_granted('SECTION_CREATE')")
	 * @param Request $request
	 * @param SectionHandler $sectionHandler
	 * @return RedirectResponse|Response
	 */
	public function new(Request $request, SectionHandler $sectionHandler)
	{
		$section = new Section();

		if ($sectionHandler->handle($request, $section)) {
			return $this->redirectToRoute('app.etmf.section', []);
		}

		return $this->render('ETMF/admin/section/create.html.twig', [
			'form' => $sectionHandler->createView(),
			'edit' => false,
		]);
	}

	/**
	 * @Route("/{sectionID}/edit", name="app.etmf.section.edit", requirements={"sectionID"="\d+"})
	 * @ParamConverter("section", options={"id"="sectionID"})
	 * @Security("is_granted('SECTION_EDIT', section)")
	 * @param Request $request
	 * @param SectionHandler $sectionHandler
	 * @param Section $section
	 * @return Response
	 */
	public function edit(Request $request, SectionHandler $sectionHandler, Section $section): Response
	{
		if ($sectionHandler->handle($request, $section)) {
			return $this->redirectToRoute('app.etmf.section', []);
		}

		return $this->render('ETMF/admin/section/create.html.twig', [
			'form' => $sectionHandler->createView(),
			'edit' => true,
		]);
	}

	/**
	 * @Route("/{sectionID}/archive", name="app.etmf.section.archive", requirements={"sectionID"="\d+"})
	 * @ParamConverter("section", options={"id"="sectionID"})
	 * @Security("is_granted('SECTION_ARCHIVE', section)")
	 * @param Section $section
	 * @return Response
	 */
	public function archive(Section $section): Response
	{
		if ($section->hasDocuments()) {

			$this->addFlash('warning', 'ARCHIVAGE IMPOSSIBLE : cette section possède déjà des documents!');

		} else {

			$entityManager = $this->getDoctrine()->getManager();
			$section->setDeletedAt(new \DateTime());

			$entityManager->persist($section);
			$entityManager->flush();
		}

		return $this->redirectToRoute('app.etmf.section', []);
	}

	/**
	 * @Route("/{sectionID}/restore", name="app.etmf.section.restore", requirements={"sectionID"="\d+"})
	 * @ParamConverter("section", options={"id"="sectionID"})
	 * @Security("is_granted('SECTION_RESTORE', section)")
	 * @param Section $section
	 * @return Response
	 */
	public function restore(Section $section): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$section->setDeletedAt(null);

		$entityManager->persist($section);
		$entityManager->flush();

		return $this->redirectToRoute('app.etmf.section', []);
	}
}
