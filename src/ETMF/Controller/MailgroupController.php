<?php

namespace App\ETMF\Controller;

use App\ESM\Service\ListGen\ListGenFactory;
use App\ETMF\Entity\Mailgroup;
use App\ETMF\Form\MailgroupType;
use App\ETMF\ListGen\MailgroupList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/etmf/mailgroup")
 */
class MailgroupController extends AbstractController
{
	/**
	 * @Route("/", name="app.etmf.mailgroup.list")
	 * @param ListGenFactory $lgm
	 * @param TranslatorInterface $translator
	 * @return Response
	 */
	public function index(ListGenFactory $lgm, TranslatorInterface $translator): Response
	{
		$list = $lgm->getListGen(MailgroupList::class);

		return $this->render('ETMF/admin/mailgroup/index.html.twig', [
			'list' => $list->getList($translator),
		]);
	}

	/**
	 * @Route("/list/ajax", name="app.etmf.mailgroup.list.ajax")
	 */
	public function ListMailgroupAjax(Request $request, ListGenFactory $lgm, TranslatorInterface $translator): Response
	{
		$list = $lgm->getListGen(MailgroupList::class);
		$list = $list->getList($translator);
		$list->setRequestParams($request->query);

		return $list->generateResponse();
	}

	/**
	 * @Route("/new", name="app.etmf.mailgroup.new")
	 */
	public function newMailgroup(Request $request): Response
	{
		$mailgroup = new Mailgroup();

		$form = $this->createForm(MailgroupType::class, $mailgroup, []);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$mailgroup = $form->getData();

			try {

				$em = $this->getDoctrine()->getManager();
				$em->persist($mailgroup);
				$em->flush();

			} catch (\Exception $exception) {

				$this->addFlash('error', 'problème lors de l\'enregistrement de la liste de diffusion : ' . $exception->getMessage());

				return $this->render('ETMF/admin/mailgroup/create.html.twig', [
					'form' => $form->createView()
				]);
			}

			$this->addFlash('success', 'Enregistrement effectué !');

			return $this->redirectToRoute('app.etmf.mailgroup.list');
		}

		return $this->render('ETMF/admin/mailgroup/create.html.twig', [
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/edit/{mailgroupID}", name="app.etmf.mailgroup.edit", methods={"GET", "POST"}, requirements={"mailgroupID"="\d+"})
	 * @ParamConverter("mailgroup", options={"id"="mailgroupID"})
	 */
	public function editMailgroup(Request $request, Mailgroup $mailgroup): Response
	{
		$form = $this->createForm(MailgroupType::class, $mailgroup, []);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$mailgroup = $form->getData();

			try {

				$em = $this->getDoctrine()->getManager();
				$em->persist($mailgroup);
				$em->flush();

			} catch (\Exception $exception) {

				$this->addFlash('error', 'problème lors de l\'enregistrement de la liste de diffusion : ' . $exception->getMessage());

				return $this->render('ETMF/admin/mailgroup/edit.html.twig', [
					'form' => $form->createView()
				]);
			}

			$this->addFlash('success', 'Enregistrement effectué !');

			return $this->redirectToRoute('app.etmf.mailgroup.list');
		}

		return $this->render('ETMF/admin/mailgroup/edit.html.twig', [
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/delete/{mailgroupID}", name="app.etmf.mailgroup.delete", methods={"GET"}, requirements={"mailgroupID"="\d+"})
	 * @ParamConverter("mailgroup", options={"id"="mailgroupID"})
	 */
	public function deleteMailgroup(Mailgroup $mailgroup): RedirectResponse
	{
		try {

			$em = $this->getDoctrine()->getManager();
			$mailgroup->setDeletedAt(new \DateTime());
			$em->persist($mailgroup);
			$em->flush();

			$this->addFlash('success', 'enregistrement effectué !');

			return $this->redirectToRoute('app.etmf.mailgroup.list');

		} catch (\Exception $exception) {

			$this->addFlash('error', 'problème lors de la suppression de la liste de diffusion : ' . $exception->getMessage());

			return $this->redirectToRoute('app.etmf.mailgroup.list');
		}
	}
}