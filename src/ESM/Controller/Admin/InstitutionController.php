<?php

namespace App\ESM\Controller\Admin;

use App\ESM\Entity\DocumentTransverse;
use App\ESM\Entity\Institution;
use App\ESM\FormHandler\DocumentTransverseHandler;
use App\ESM\FormHandler\InstitutionHandler;
use App\ESM\ListGen\Admin\InstitutionList;
use App\ESM\Repository\InterlocutorRepository;
use App\ESM\Service\ListGen\ListGenFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/institutions")
 */
class InstitutionController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="admin.institutions.index")
     * @Security("is_granted('INSTITUTION_LIST')")
     */
    public function index(Request $request, ListGenFactory $lgm, TranslatorInterface $translator): Response
    {
        $displayArchived = in_array('ROLE_INSTITUTION_ARCHIVE', $this->getUser()->getRoles(), true);

        $list = $lgm->getListGen(InstitutionList::class);

        return $this->render('admin/institution/index.html.twig', [
            'list' => $list->getList($displayArchived, $translator),
        ]);
    }

    /**
     * @Route("/admin/ajax/institutions", name="admin.institutions.index.ajax")
     * @Security("is_granted('INSTITUTION_LIST')")
     */
    public function indexAjax(Request $request, ListGenFactory $lgm, TranslatorInterface $translator): Response
    {
        $displayArchived = in_array('ROLE_INSTITUTION_ARCHIVE', $this->getUser()->getRoles(), true);

        // listgen handle request
        $list = $lgm->getListGen(InstitutionList::class);
        $list = $list->getList($displayArchived, $translator);
        $list->setRequestParams($request->query);

        // json response
        return $list->generateResponse();
    }

    /**
     * @Route("/new", name="admin.institution.new")
     * @Security("is_granted('INSTITUTION_CREATE')")
     */
    public function new(Request $request, InstitutionHandler $institutionHandler): Response
    {
        $institution = new Institution();

        if ($institutionHandler->handle($request, $institution)) {
            return $this->redirectToRoute('admin.institution.show', ['id' => $institution->getId()]);
        }

        return $this->render('admin/institution/create.html.twig', [
            'form' 			 => $institutionHandler->createView(),
            'action' 		 => 'create',
            'typeIdNoFiness' => InstitutionHandler::typeIdNoFiness,
        ]);
    }

    /**
     * @Route("/{id}/show", name="admin.institution.show", requirements={"id"="\d+"})
     * @Security("is_granted('INSTITUTION_SHOW', institution)")
     */
    public function show(Request $request, Institution $institution, InterlocutorRepository $interlocutorRepository): Response
    {
        // render
        return $this->render('admin/institution/show.html.twig', [
            'institution' => $institution,
            'typeIdNoFiness' => InstitutionHandler::typeIdNoFiness,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin.institution.edit", requirements={"id"="\d+"})
     * @Security("is_granted('INSTITUTION_EDIT', institution)")
     */
    public function edit(Request $request, Institution $institution, InstitutionHandler $institutionHandler): Response
    {
        if ($institutionHandler->handle($request, $institution)) {
            return $this->redirectToRoute('admin.institution.show', [
                'id' => $institution->getId(),
            ]);
        }

        return $this->render('admin/institution/create.html.twig', [
            'form' => $institutionHandler->createView(),
            'action' => 'edit',
            'typeIdNoFiness' => InstitutionHandler::typeIdNoFiness,
            'edit' => true,
        ]);
    }

    /**
     * @Route("{id}/archive", name="admin.institution.archive", requirements={"id"="\d+"})
     * @Security("is_granted('INSTITUTION_ARCHIVE', institution)")
     */
    public function archive(Request $request, Institution $institution): Response
    {
        $institution->setDeletedAt(new \DateTime());
        $this->entityManager->persist($institution);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin.institution.show', [
            'id' => $request->get('id'),
        ]);
    }

    /**
     * @Route("{id}/restore", name="admin.institution.restore", requirements={"id"="\d+"})
     * @Security("is_granted('INSTITUTION_RESTORE', institution)")
     */
    public function restore(Request $request, Institution $institution): Response
    {
        $institution->setDeletedAt(null);
        $this->entityManager->persist($institution);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin.institution.show', [
            'id' => $request->get('id'),
        ]);
    }

    /**
     * @Route("/{id}/add-transverse-document", name="admin.institution.addTransverseDocument", requirements={"id"="\d+"})
     * @Security("is_granted('DOCUMENTTRANSVERSE_CREATE', institution)")
     */
    public function addTransverseDocument(Request $request, Institution $institution, DocumentTransverseHandler $documentTransverseHandler): Response
    {
        $document = new DocumentTransverse();
        $document->setInstitution($institution);
        $document->setIsValid(true);

        if ($documentTransverseHandler->handle($request, $document)) {
            return $this->redirectToRoute('admin.institution.show', ['id' => $institution->getId()]);
        }

        return $this->render('document_transverse/create.html.twig', [
            'institution' => $institution,
            'form' => $documentTransverseHandler->createView(),
            'action' => 'create',
            'edit' => false,
        ]);
    }
}
