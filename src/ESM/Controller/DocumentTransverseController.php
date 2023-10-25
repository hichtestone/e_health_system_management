<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Center;
use App\ESM\Entity\DocumentTransverse;
use App\ESM\Entity\Drug;
use App\ESM\Entity\Institution;
use App\ESM\Entity\Interlocutor;
use App\ESM\Entity\PorteeDocumentTransverse;
use App\ESM\Entity\Project;
use App\ESM\Entity\ProjectTrailTreatment;
use App\ESM\Entity\User;
use App\ESM\FormHandler\DocumentTransverseHandler;
use App\ESM\Repository\PorteeDocumentTransverseRepository;
use App\ESM\Repository\TypeDocumentTransverseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DocumentTransverseController
 * @package App\ESM\Controller
 */
class DocumentTransverseController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/documents", name="document_transverse.index")
     * @Security("is_granted('ROLE_DOCUMENTTRANSVERSE_READ')")
     */
    public function index(Request $request, PaginatorInterface $paginator, PorteeDocumentTransverseRepository $porteeDocumentTransverseRepository, TypeDocumentTransverseRepository $typeDocumentTransverseRepository): Response
    {
        // roles
        $roles = $this->getUser()->getRoles();

        // Nombre de resultats par page
        $rows_count = 20;

        // Query
        $filter = !empty($request->query->all()) ? $request->query->all() : [];

        //$filter = $filter['filter'] ?? [];
        $query_string = [];

        $documentsbyDrug = $documentsbyInstitution = $documentsbyInterlocultor = $documents = [];

        $user = $this->entityManager->getRepository(User::class)->find($this->getUser()->getId());

        // Choix > Global

        /*if (in_array('ROLE_DOCUMENTTRANSVERSE_READ', $roles) && in_array('ROLE_DRUG_READ', $roles) && !in_array('ROLE_INTERLOCUTOR_READ', $roles) && !in_array('ROLE_INSTITUTION_READ', $roles)) {
            $documents = $this->getDoctrine()->getRepository(DocumentTransverse::class)->findAllQuery($user, $filter, $roles, 'drug');
        } elseif (in_array('ROLE_DOCUMENTTRANSVERSE_READ', $roles) && in_array('ROLE_INTERLOCUTOR_READ', $roles) && !in_array('ROLE_INSTITUTION_READ', $roles) && !in_array('ROLE_DRUG_READ', $roles)) {
            $documents = $this->getDoctrine()->getRepository(DocumentTransverse::class)->findAllQuery($user, $filter, $roles, 'interlocutor');
        } elseif (in_array('ROLE_DOCUMENTTRANSVERSE_READ', $roles) && in_array('ROLE_INSTITUTION_READ', $roles) && !in_array('ROLE_INTERLOCUTOR_READ', $roles) && !in_array('ROLE_DRUG_READ', $roles)) {
            $documents = $this->getDoctrine()->getRepository(DocumentTransverse::class)->findAllQuery($user, $filter, $roles, 'institution');
        } else {
            $documents = $this->getDoctrine()->getRepository(DocumentTransverse::class)->findAllQuery($user, $filter, $roles, '');
        }*/

        if (in_array('ROLE_DOCUMENTTRANSVERSE_READ', $roles) && (in_array('ROLE_INTERLOCUTOR_READ', $roles) || in_array('ROLE_INSTITUTION_READ', $roles) || in_array('ROLE_DRUG_READ', $roles))) {
            $documents = $this->getDoctrine()->getRepository(DocumentTransverse::class)->findAllQuery($user, $filter, $roles, '');
        }

        $documentsbyDrug = $documentsbyInstitution = $documentsbyInterlocultor = [];

        // Choix > mes projets
        if (!empty($filter['filter']['option']) && 'myproject' === $filter['filter']['option']) {
            // mes documents liées aux médicaments
            if (in_array('ROLE_DOCUMENTTRANSVERSE_READ', $roles) && in_array('ROLE_PROJECT_SETTINGS_READ', $roles)) {
                $documentsbyDrug = $this->getDoctrine()->getRepository(DocumentTransverse::class)->findAllQuery($user, $filter, $roles, 'drug');
            }

            // mes documents  liées aux institutions
            if (in_array('ROLE_DOCUMENTTRANSVERSE_READ', $roles) && in_array('ROLE_CENTER_READ', $roles)) {
                $documentsbyInstitution = $this->getDoctrine()->getRepository(DocumentTransverse::class)->findAllQuery($user, $filter, $roles, 'institution');
            }

            // mes documents liées aux interlocutors
            if (in_array('ROLE_DOCUMENTTRANSVERSE_READ', $roles) && in_array('ROLE_PROJECT_INTERLOCUTOR_READ', $roles)) {
                $documentsbyInterlocultor = $this->getDoctrine()->getRepository(DocumentTransverse::class)->findAllQuery($user, $filter, $roles, 'interlocutor');
            }

            $documents = array_merge($documentsbyDrug, $documentsbyInstitution, $documentsbyInterlocultor);
        }

        $documentPaginator1 = $paginator->paginate(
            $documents,
            $request->query->getInt('page', 1),
            $rows_count
        );

        return $this->render('document_transverse/index.html.twig', [
            'documents' => $documentPaginator1,
            'filter' => $filter,
            'portee_list' => $porteeDocumentTransverseRepository->findAll(),
            'type_list' => $typeDocumentTransverseRepository->findAll(),
            'role_download_institution' => in_array('ROLE_DOCUMENTTRANSVERSE_READ', $roles) && (in_array('ROLE_INSTITUTION_READ', $roles) || in_array('ROLE_CENTER_READ', $roles)),
            'role_download_interlocutor' => in_array('ROLE_DOCUMENTTRANSVERSE_READ', $roles) && (in_array('ROLE_INTERLOCUTOR_READ', $roles) || in_array('ROLE_PROJECT_INTERLOCUTOR_READ', $roles)),
            'role_download_drug' => in_array('ROLE_DOCUMENTTRANSVERSE_READ', $roles) && (in_array('ROLE_DRUG_READ', $roles) || in_array('ROLE_PROJECT_SETTINGS_READ', $roles)),
        ]);
    }

    /**
     * @Route("/{id}/archive", name="document_transverse.archive_restore", methods="GET", requirements={"id"="\d+"})
     *
     * @return Response
     */
    public function archive_restore(DocumentTransverse $document)
    {
        if (null == $document->getDeletedAt()) {
            $document->setDeletedAt(new \DateTime());
            $document->setIsValid(false);
        } else {
            $document->setDeletedAt(null);
			$document->setIsValid(true);
        }
        $this->getDoctrine()->getManager()->flush();

        // retour en fonction du contexte
        $context_interlocutor = null != $document->getInterlocutor(); // admin.interlocutor.show
        $context_institution = null != $document->getInstitution(); // admin.institution.show
        $context_drug = null != $document->getDrug(); // admin.drug.show

        // redirection vers la fiche de l'interlocuteur
        if ($context_interlocutor) {
            return $this->redirectToRoute('admin.interlocutor.show', [
                'id' => $document->getInterlocutor()->getId(),
            ]);
        }

        // redirection vers la fiche de l'etablissement
        if ($context_institution) {
            return $this->redirectToRoute('admin.institution.show', [
                'id' => $document->getInstitution()->getId(),
            ]);
        }

        // redirection vers la fiche du medicament
        if ($context_drug) {
            return $this->redirectToRoute('admin.drug.show', [
                'id' => $document->getDrug()->getId(),
            ]);
        }

        // Redirection vers index
        return $this->redirectToRoute('document_transverse.index');
    }

    /**
     * @Route("/{id}/show", name="document_transverse.show", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('ROLE_DOCUMENTTRANSVERSE_READ', document)")
     *
     * @return Response
     */
    public function show(DocumentTransverse $document)
    {
        return $this->render('document_transverse/fiche.html.twig', [
            'document' => $document,
            'homeShow' => true,
            'action' => 'show',
        ]);
    }

    /**
     * @Route("/{id}/edit", name="document_transverse.edit", requirements={"id"="\d+"})
     * @Security("is_granted('ROLE_DOCUMENTTRANSVERSE_WRITE')")
     */
    public function edit(Request $request, DocumentTransverse $documentTransverse, DocumentTransverseHandler $documentTransverseHandler): Response
    {
        if ($documentTransverseHandler->handle($request, $documentTransverse)) {
            return $this->redirectToRoute('document_transverse.index', [
                'id' => $documentTransverse->getId(),
            ]);
        }

        return $this->render('document_transverse/create.html.twig', [
            'form' => $documentTransverseHandler->createView(),
            'action' => 'edit',
            'edit' => true,
            'document' => $documentTransverse,
        ]);
    }

    /**
     * @Route("/projects/{project}/selection/{center}/document/{id}/show", name="document_transverse_center.show", methods="GET", requirements={"project"="\d+","center"="\d+","id"="\d+"})
     * @Security("is_granted('ROLE_DOCUMENTTRANSVERSE_READ', document)")
     *
     * @return Response
     */
    public function center_show(Project $project, Center $center, DocumentTransverse $document)
    {
        return $this->render('document_transverse/fiche.html.twig', [
            'project' => $project,
            'center' => $center,
            'document' => $document,
            'homeShow' => true,
            'action' => 'show',
        ]);
    }

    /**
     * @Route("/admin/drugs/{drug}/document/{id}/show", name="document_transverse_drug.show", methods="GET", requirements={"drug"="\d+","id"="\d+"})
     * @Security("is_granted('DRUG_LIST_DOCUMENTTRANSVERSE', drug)")
     *
     * @return Response
     */
    public function drug_show(DocumentTransverse $document, Drug $drug)
    {
        return $this->render('document_transverse/fiche.html.twig', [
            'drug' => $drug,
            'document' => $document,
            'homeShow' => true,
            'action' => 'show',
        ]);
    }

    /**
     * @Route("/admin/drugs/{id}/document/new", name="admin.drug.document.new", requirements={"id"="\d+"})
     * @Security("is_granted('DRUG_READ_WRITE_ARCHIVE_DOCUMENTTRANSVERSE', drug)")
     *
     * @return Response
     */
    public function drug_new(Request $request, Drug $drug, DocumentTransverseHandler $documentTransverseHandler)
    {
        /** @var PorteeDocumentTransverse $portee */
        $portee = $this->getDoctrine()->getRepository(PorteeDocumentTransverse::class)->findOneBy([
            'code' => 'MEDICAMENT',
        ]);

        $document = new DocumentTransverse();
        $document->setDrug($drug);
        $document->setIsValid(true);
        $drug->setIsValid(true);
        $document->setPorteeDocument($portee);

        if ($documentTransverseHandler->handle($request, $document)) {
            return $this->redirectToRoute('admin.drug.show', ['id' => $drug->getId()]);
        }

        return $this->render('document_transverse/create.html.twig', [
            'drug' => $drug,
            'form' => $documentTransverseHandler->createView(),
            'action' => 'create',
            'edit' => false,
        ]);
    }

    /**
     * @Route("/admin/drugs/{drug}/document/{id}/edit", name="document_transverse_drug.edit", requirements={"drug"="\d+","id"="\d+"})
     * @Security("is_granted('DRUG_READ_WRITE_ARCHIVE_DOCUMENTTRANSVERSE', drug)")
     */
    public function drug_edit(Request $request, DocumentTransverse $documentTransverse, DocumentTransverseHandler $documentTransverseHandler, Drug $drug): Response
    {
        if ($documentTransverseHandler->handle($request, $documentTransverse)) {
            return $this->redirectToRoute('document_transverse_drug.show', [
                'drug' => $drug->getId(),
                'id' => $documentTransverse->getId(),
            ]);
        }

        return $this->render('document_transverse/create.html.twig', [
            'form' => $documentTransverseHandler->createView(),
            'action' => 'edit',
            'edit' => true,
            'document' => $documentTransverse,
        ]);
    }

	/**
	 * @param int $id
	 * @return Response
	 */
    public function drug_single_list(int $id): Response
	{
        /** @var Drug $drug */
        $drug = $this->getDoctrine()->getRepository(Drug::class)->find($id);

        // Blocage archivage si medicament dans un projet
        $treatment_entities = $this->getDoctrine()->getRepository(ProjectTrailTreatment::class)->findOneBy([
            'drug' => $drug,
        ]);

        $is_archive_allowed = null == $treatment_entities;

        return $this->render('document_transverse/list/drug_single_list.html.twig', [
            'drug' => $drug,
            'is_archive_allowed' => $is_archive_allowed,
        ]);
    }

    /**
     * @Route("/admin/institutions/{institution}/document/{id}/show", name="document_transverse_institution.show", methods="GET", requirements={"institution"="\d+","id"="\d+"})
     * @Security("is_granted('INSTITUTION_LIST_DOCUMENTTRANSVERSE', institution)")
     *
     * @return Response
     */
    public function institution_show(Institution $institution, DocumentTransverse $document)
    {
        return $this->render('document_transverse/fiche.html.twig', [
            'institution' => $institution,
            'document' => $document,
            'homeShow' => true,
            'action' => 'show',
        ]);
    }

    /**
     * @Route("/admin/institutions/{id}/add-transverse-document", name="admin.institution.addTransverseDocument", requirements={"id"="\d+"})
     * @Security("is_granted('INSTITUTION_READ_WRITE_ARCHIVE_DOCUMENTTRANSVERSE', institution)")
     *
     * @return Response
     */
    public function institution_new(Request $request, Institution $institution, DocumentTransverseHandler $documentTransverseHandler)
    {
        /** @var PorteeDocumentTransverse $portee */
        $portee = $this->getDoctrine()->getRepository(PorteeDocumentTransverse::class)->findOneBy([
            'code' => 'INSTITUTION',
        ]);

        $document = new DocumentTransverse();
        $document->setInstitution($institution);
        $document->setIsValid(true);
        $document->setPorteeDocument($portee);

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

    /**
     * @Route("/admin/institutions/{institution}/document/{id}/edit", name="document_transverse_institution.edit", requirements={"institution"="\d+","id"="\d+"})
     * @Security("is_granted('INSTITUTION_READ_WRITE_ARCHIVE_DOCUMENTTRANSVERSE', institution)")
     */
    public function institution_edit(Request $request, DocumentTransverse $documentTransverse, DocumentTransverseHandler $documentTransverseHandler, Institution $institution): Response
    {
        if ($documentTransverseHandler->handle($request, $documentTransverse)) {
            return $this->redirectToRoute('document_transverse_institution.show', [
                'institution' => $institution->getId(),
                'id' => $documentTransverse->getId(),
            ]);
        }

        return $this->render('document_transverse/create.html.twig', [
            'form' => $documentTransverseHandler->createView(),
            'action' => 'edit',
            'edit' => true,
            'document' => $documentTransverse,
        ]);
    }

    public function institution_single_list(int $id)
    {
        /** @var Institution $institution */
        $institution = $this->getDoctrine()->getRepository(Institution::class)->find($id);

        return $this->render('document_transverse/list/institution_single_list.html.twig', [
            'institution' => $institution,
        ]);
    }

    /**
     * @Route("/admin/interlocutors/{interlocutor}/document/{id}/show", name="document_transverse_interlocuteur.show", methods="GET", requirements={"interlocutor"="\d+","id"="\d+"})
     * @Security("is_granted('INTERLOCUTOR_LIST_DOCUMENTTRANSVERSE', interlocutor)")
     *
     * @return Response
     */
    public function interlocutor_show(Interlocutor $interlocutor, DocumentTransverse $document)
    {
        return $this->render('document_transverse/fiche.html.twig', [
            'interlocutor' => $interlocutor,
            'document' => $document,
            'homeShow' => true,
            'action' => 'show',
        ]);
    }

    /**
     * @Route("/admin/interlocutors/{id}/add-transverse-document", name="admin.interlocutor.addTransverseDocument", requirements={"id"="\d+"})
     * @Security("is_granted('INTERLOCUTOR_READ_WRITE_ARCHIVE_DOCUMENTTRANSVERSE', interlocutor)")
     *
     * @return Response
     */
    public function interlocutor_new(Interlocutor $interlocutor, DocumentTransverseHandler $documentTransverseHandler, Request $request)
    {
        /** @var PorteeDocumentTransverse $portee */
        $portee = $this->getDoctrine()->getRepository(PorteeDocumentTransverse::class)->findOneBy([
            'code' => 'INTERLOCUTOR',
        ]);

        $document = new DocumentTransverse();
        $document->setInterlocutor($interlocutor);
        $document->setIsValid(true);
        $document->setPorteeDocument($portee);

        if ($documentTransverseHandler->handle($request, $document)) {
            return $this->redirectToRoute('admin.interlocutor.show', [
                'id' => $interlocutor->getId(),
            ]);
        }

        return $this->render('document_transverse/create.html.twig', [
            'interlocutor' => $interlocutor,
            'form' => $documentTransverseHandler->createView(),
            'action' => 'create',
            'edit' => false,
        ]);
    }

    /**
     * @Route("/admin/interlocutors/{interlocutor}/document/{id}/edit", name="document_transverse_interlocutor.edit", requirements={"interlocutor"="\d+","id"="\d+"})
     * @Security("is_granted('INTERLOCUTOR_READ_WRITE_ARCHIVE_DOCUMENTTRANSVERSE', interlocutor)")
     */
    public function interlocutor_edit(Request $request, DocumentTransverse $documentTransverse, DocumentTransverseHandler $documentTransverseHandler, Interlocutor $interlocutor): Response
    {
        if ($documentTransverseHandler->handle($request, $documentTransverse)) {
            return $this->redirectToRoute('document_transverse_interlocuteur.show', [
                'interlocutor' => $interlocutor->getId(),
                'id' => $documentTransverse->getId(),
            ]);
        }

        return $this->render('document_transverse/create.html.twig', [
            'form' => $documentTransverseHandler->createView(),
            'action' => 'edit',
            'edit' => true,
            'document' => $documentTransverse,
        ]);
    }

    public function interlocutor_single_list(int $id)
    {
        /** @var Interlocutor $interlocutor */
        $interlocutor = $this->getDoctrine()->getRepository(Interlocutor::class)->find($id);

        return $this->render('document_transverse/list/interlocutor_single_list.html.twig', [
            'interlocutor' => $interlocutor,
        ]);
    }
}
