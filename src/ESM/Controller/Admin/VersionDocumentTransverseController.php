<?php

namespace App\ESM\Controller\Admin;

use App\ESM\Entity\DocumentTransverse;
use App\ESM\Entity\Drug;
use App\ESM\Entity\User;
use App\ESM\Entity\VersionDocumentTransverse;
use App\ESM\FormHandler\VersionDocumentTransverseHandler;
use App\ESM\Notification\DocumentNotificationAlert;
use App\ESM\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class VersionDocumentTransverseController extends AbstractController
{
    /**
     * @Route("/admin/drugs/{drug}/document/{id}/new", name="document_transverse.version.new", requirements={"drug"="\d+","id"="\d+"})
     * @Security("is_granted('DRUG_READ_WRITE_ARCHIVE_DOCUMENTTRANSVERSE', drug)")
     *
     * @return Response
     */
    public function newVersionDocument(Request $request, DocumentTransverse $documentTransverse, VersionDocumentTransverseHandler $versionDocumentTransverseHandler, DocumentNotificationAlert $documentNotificationAlert, UserRepository $userRepository, Drug $drug)
    {
        $user = $userRepository->find($this->getUser()->getId());
        $respo = $this->getDoctrine()->getRepository(Drug::class)->GetDrugByProject($user, $drug);
        $version = new VersionDocumentTransverse();
        $version->setDocumentTransverse($documentTransverse);
        $documentTransverse->addVersionDocumentTransverse($version);
        $version->setIsValid(false);
        if ($versionDocumentTransverseHandler->handle($request, $version)) {
            if (null !== $respo) {
                //Récuperer la liste des chefs du projet
                $projectsManager = $this->getDoctrine()->getRepository(User::class)->indexUserByProfile();
                foreach ($projectsManager as $projectManager) {
                    $documentNotificationAlert->notify($this->getUser(), $request, 'Alerte mise à jour de document médicament', 'Document_Notification', $projectManager->getEmail(), ['user' => $user, 'version' => $version->getVersion(), 'document' => $documentTransverse, 'drug' => $documentTransverse->getDrug()]);
                }
            }

            return $this->redirectToRoute('document_transverse_drug.show', ['drug' => $documentTransverse->getDrug()->getId(), 'id' => $documentTransverse->getId()]);
        }

        return $this->render('document_transverse/version_document/create.html.twig', [
            'document' => $documentTransverse,
            'form' => $versionDocumentTransverseHandler->createView(),
            'action' => 'create',
            'edit' => false,
        ]);
    }

    /**
     * @Route("/documents/{id}/version/show", name="document_transverse.version.show", methods="GET", requirements={"id"="\d+"})
     *
     * @return Response
     */
    public function showVersionDocument(DocumentTransverse $document, RouterInterface $router)
    {
        return $this->json([
            'html' => $this->renderView('document_transverse/version_document/show.html.twig', [
                'document' => $document,
                'action' => 'show',
                'url' => $router->generate('document_transverse.version.show', ['id' => $document->getId()]),
            ]),
        ]);
    }

    /**
     * @Route("/admin/drugs/{drug}/document/{id}/edit/{version}", name="document_transverse.version.edit", requirements={"drug"="\d+","id"="\d+","version"="\d+"})
     * @Security("is_granted('DRUG_READ_WRITE_ARCHIVE_DOCUMENTTRANSVERSE', drug)")
     *
     * @return Response
     */
    public function edit(Request $request, DocumentTransverse $documentTransverse, VersionDocumentTransverseHandler $versionDocumentTransverseHandler, DocumentNotificationAlert $documentNotificationAlert, UserRepository $userRepository, VersionDocumentTransverse $version, Drug $drug)
    {
        $user = $userRepository->find($this->getUser()->getId());
        $respo = $this->getDoctrine()->getRepository(Drug::class)->GetDrugByProject($user, $drug);

        if ($versionDocumentTransverseHandler->handle($request, $version)) {
            if (null !== $respo) {
                //Récuperer la liste des chefs du projet
                $projectsManager = $this->getDoctrine()->getRepository(User::class)->indexUserByProfile();
                foreach ($projectsManager as $projectManager) {
                    $documentNotificationAlert->notify($this->getUser(), $request, 'Alerte mise à jour de document médicament', 'Document_Notification', $projectManager->getEmail(), ['user' => $user, 'version' => $version->getVersion(), 'document' => $documentTransverse, 'drug' => $documentTransverse->getDrug()]);
                }
            }

            return $this->redirectToRoute('document_transverse_drug.show', ['drug' => $documentTransverse->getDrug()->getId(), 'id' => $documentTransverse->getId()]);
        }

        return $this->render('document_transverse/version_document/edit.html.twig', [
            'document' => $documentTransverse,
            'form' => $versionDocumentTransverseHandler->createView(),
            'action' => 'edit',
            'edit' => true,
            'version' => $version,
        ]);
    }
}
