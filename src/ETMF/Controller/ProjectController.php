<?php

namespace App\ETMF\Controller;

use App\ESM\ListGen\ProjectUserList;
use App\ESM\Service\ListGen\ListGenFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/etmf/project")
 */
class ProjectController extends AbstractController
{
	/**
	 * @Route("/", name="app.etmf.project.index")
	 * @param Request $request
	 * @param ListGenFactory $lgm
	 * @return Response
	 */
	public function index(Request $request, ListGenFactory $lgm): Response
	{
		$list = $lgm->getListGen(ProjectUserList::class);

		return $this->render('ETMF/admin/project/index.html.twig', [
			'list' => $list->getList($this->getUser(), $request->getLocale()),
			'home' => true,
		]);
	}
}
