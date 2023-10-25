<?php

namespace App\ETMF\Controller;

use App\ESM\Service\ListGen\ListGenFactory;
use App\ETMF\Entity\Artefact;
use App\ETMF\FormHandler\ArtefactHandler;
use App\ETMF\ListGen\ArtefactList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/etmf/structure/artefact")
 */
class ArtefactController extends AbstractController
{
	/**
	 * @Route("/", name="app.etmf.artefact")
	 * @param ListGenFactory $lgm
	 * @param TranslatorInterface $translator
	 * @return Response
	 */
    public function index(ListGenFactory $lgm, TranslatorInterface $translator): Response
    {
		$list = $lgm->getListGen(ArtefactList::class);

		return $this->render('ETMF/admin/artefact/index.html.twig', [
			'activeMenu' => 'etmf-artefact',
			'list' 		 => $list->getList($translator),
		]);
	}

	/**
	 * @Route("/ajax", name="app.etmf.artefact.ajax")
	 * @param Request $request
	 * @param ListGenFactory $lgm
	 * @param TranslatorInterface $translator
	 * @return mixed
	 */
	public function indexAjax(Request $request, ListGenFactory $lgm, TranslatorInterface $translator)
	{
		$list = $lgm->getListGen(ArtefactList::class);
		$list = $list->getList($translator);
		$list->setRequestParams($request->query);

		return $list->generateResponse();
	}

	/**
	 * @Route("/new", name="app.etmf.artefact.new")
	 * @param Request $request
	 * @param ArtefactHandler $artefactHandler
	 * @return RedirectResponse|Response
	 */
	public function new(Request $request, ArtefactHandler $artefactHandler)
	{
		$artefact = new Artefact();

		if ($artefactHandler->handle($request, $artefact)) {
			return $this->redirectToRoute('app.etmf.artefact', []);
		}

		return $this->render('ETMF/admin/artefact/create.html.twig', [
			'form' => $artefactHandler->createView(),
			'edit' => false,
		]);
	}

	/**
	 * @Route("/{artefactID}/edit", name="app.etmf.artefact.edit", requirements={"artefactID"="\d+"})
	 * @ParamConverter("artefact", options={"id"="artefactID"})
	 * @param Request $request
	 * @param ArtefactHandler $artefactHandler
	 * @param Artefact $artefact
	 * @return Response
	 */
	public function edit(Request $request, ArtefactHandler $artefactHandler, Artefact $artefact): Response
	{
		if ($artefactHandler->handle($request, $artefact)) {
			return $this->redirectToRoute('app.etmf.artefact', []);
		}

		return $this->render('ETMF/admin/artefact/create.html.twig', [
			'form' => $artefactHandler->createView(),
			'edit' => true,
		]);
	}

	/**
	 * @Route("/{artefactID}/archive", name="app.etmf.artefact.archive", requirements={"artefactID"="\d+"})
	 * @ParamConverter("artefact", options={"id"="artefactID"})
	 * @param Artefact $artefact
	 * @return Response
	 */
	public function archive(Artefact $artefact): Response
	{
		if ($artefact->hasDocuments()) {
			$this->addFlash('warning', 'ARCHIVAGE IMPOSSIBLE : cet artefact possède déjà des documents!');
		} else {

			$entityManager = $this->getDoctrine()->getManager();
			$artefact->setDeletedAt(new \DateTime());

			$entityManager->persist($artefact);
			$entityManager->flush();
		}

		return $this->redirectToRoute('app.etmf.artefact', []);
	}

	/**
	 * @Route("/{artefactID}/restore", name="app.etmf.artefact.restore", requirements={"artefactID"="\d+"})
	 * @ParamConverter("artefact", options={"id"="artefactID"})
	 * @param Artefact $artefact
	 * @return Response
	 */
	public function restore(Artefact $artefact): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$artefact->setDeletedAt(null);

		$entityManager->persist($artefact);
		$entityManager->flush();

		return $this->redirectToRoute('app.etmf.artefact', []);
	}
}
