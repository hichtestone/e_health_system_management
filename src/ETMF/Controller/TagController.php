<?php

namespace App\ETMF\Controller;

use App\ESM\Service\ListGen\ListGenFactory;
use App\ETMF\Entity\Tag;
use App\ETMF\Form\TagType;
use App\ETMF\ListGen\TagList;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/etmf/tags")
 */
class TagController extends AbstractController
{
	/**
	 * @Route("/", name="app.etmf.tag.list")
	 * @Security("is_granted('TAG_LIST')")
	 * @param ListGenFactory $lgm
	 * @return Response
	 */
	public function index(ListGenFactory $lgm): Response
	{
		$list = $lgm->getListGen(TagList::class);

		return $this->render('ETMF/admin/tag/index.html.twig', [
			'activeMenu' => 'etmf-tag',
			'list' 		 => $list->getList(),
		]);
	}

	/**
	 * @Route("/ajax", name="app.etmf.tag.ajax")
	 * @Security("is_granted('TAG_LIST')")
	 * @param Request $request
	 * @param ListGenFactory $lgm
	 * @return mixed
	 */
	public function indexAjax(Request $request, ListGenFactory $lgm)
	{
		$list = $lgm->getListGen(TagList::class);
		$list = $list->getList();
		$list->setRequestParams($request->query);

		return $list->generateResponse();
	}

	/**
	 * @Route("/new", name="app.etmf.tag.new")
	 * @Security("is_granted('ZONE_CREATE')")
	 * @param Request $request
	 * @return RedirectResponse|Response
	 * @throws Exception
	 */
	public function new(Request $request): Response
	{
		$tag = new Tag();

		$form = $this->createForm(TagType::class, $tag, []);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$newTag = $form->getData();

			try {

				$em = $this->getDoctrine()->getManager();
				$em->persist($newTag);
				$em->flush();

				return $this->redirectToRoute('app.etmf.tag.list', []);

			} catch (Exception $exception) {

				throw new Exception('this tag : ' . $tag->getName() . 'error to persist !');
			}
		}

		return $this->render('ETMF/admin/tag/create.html.twig', [
			'form' => $form->createView(),
			'edit' => false,
		]);
	}

	/**
	 * @Route("/{tagID}/edit", name="app.etmf.tag.edit", requirements={"tagID"="\d+"})
	 * @ParamConverter("tag", options={"id"="tagID"})
	 * @Security("is_granted('TAG_EDIT', tag)")
	 * @param Request $request
	 * @param Tag $tag
	 * @return Response
	 * @throws Exception
	 */
	public function edit(Request $request, Tag $tag): Response
	{
		$form = $this->createForm(TagType::class, $tag, []);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$tag = $form->getData();

			try {

				$em = $this->getDoctrine()->getManager();
				$em->persist($tag);
				$em->flush();

				return $this->redirectToRoute('app.etmf.tag.list', []);

			} catch (Exception $exception) {

				throw new Exception('this tag : ' . $tag->getName() . 'error to persist !');
			}
		}

		return $this->render('ETMF/admin/tag/create.html.twig', [
			'form' => $form->createView(),
			'edit' => true,
		]);
	}

	/**
	 * @Route("/{tagID}/archive", name="app.etmf.tag.archive", requirements={"tagID"="\d+"})
	 * @ParamConverter("tag", options={"id"="tagID"})
	 * @Security("is_granted('TAG_ARCHIVE', tag)")
	 * @param Tag $tag
	 * @return Response
	 */
	public function archive(Tag $tag): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$tag->setDeletedAt(new \DateTime());

		$entityManager->persist($tag);
		$entityManager->flush();

		return $this->redirectToRoute('app.etmf.tag.list', []);
	}

	/**
	 * @Route("/{tagID}/restore", name="app.etmf.tag.restore", requirements={"tagID"="\d+"})
	 * @ParamConverter("tag", options={"id"="tagID"})
	 * @Security("is_granted('TAG_RESTORE', tag)")
	 * @param Tag $tag
	 * @return Response
	 */
	public function restore(Tag $tag): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$tag->setDeletedAt(null);

		$entityManager->persist($tag);
		$entityManager->flush();

		return $this->redirectToRoute('app.etmf.tag.list', []);
	}
}
