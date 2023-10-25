<?php

namespace App\ETMF\Controller;

use App\ETMF\Entity\DocumentVersion;
use App\ETMF\Form\SearchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/etmf")
 */
class AppController extends AbstractController
{
    /**
     * @Route("/", name="app.etmf.home")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $searchForm = $this->createForm(SearchType::class, null, []);

        $searchForm->handleRequest($request);

        $all = $this->getDoctrine()->getRepository(DocumentVersion::class)->findAll();

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {

            $data = $searchForm->getData();

            $sponsors = $data['sponsor'];
            $projects = $data['project'];
            $zones = $data['zone'];
            $sections = $data['section'];
            $artefacts = $data['artefact'];
            $countries = $data['country'];
            $centers = $data['center'];
            $tags = $data['tag'];
            $status = $data['status'];

            $documentsVersion = $this->getDoctrine()->getRepository(DocumentVersion::class)->searchEngine(
                $sponsors, $projects, $zones, $sections, $artefacts, $countries, $centers, $tags, $status
            );

            return $this->render('ETMF/app/index.html.twig', [
                'activeMenu' => 'etmf',
                'searchForm' => $searchForm->createView(),
                'documentsVersion' => $documentsVersion
            ]);
        }

        return $this->render('ETMF/app/index.html.twig', [
            'activeMenu' => 'etmf',
            'searchForm' => $searchForm->createView(),
            'documentsVersion' => $all
        ]);
    }

    /**
     * @Route("/documentVersion/{id}/show", name="app.etmf.documentVersion.show", requirements={"id"="\d+"})
     * @ParamConverter("documentVersion", options={"id"="id"})
     *
     * @param DocumentVersion $documentVersion
     * @return Response
     */
    public function show(DocumentVersion $documentVersion): Response
    {

        return $this->render('ETMF/app/show.html.twig', [
            'activeMenu' => 'etmf',
            'documentVersion' => $documentVersion
        ]);
    }
}
