<?php

namespace App\ESM\Controller\Admin;

use App\ESM\ListGen\Admin\AuditTrail\ConnexionErrorList;
use App\ESM\ListGen\Admin\AuditTrail\ConnexionList;
use App\ESM\Service\ListGen\ListGenFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

/**
 * @Route("/admin/audit-trail")
 */
class AuditTrailController extends AbstractController
{
    /**
     * @Route("/{category}", name="admin.audit_trail.generic.index",
     *     requirements={"category"="user|profile|project|user_project|funding|publication|service|institution|interlocutor|center|interlocutor_center|date|rule|submission|project_database_freeze|document_tracking|document_tracking_center|document_tracking_interlocutor|courbe_setting|patient|patient_data|visit_patient|drug|document_transverse|version_document_transverse|deviation_correction|deviation|deviation_review|deviation_action|report_visit|deviation_sample|deviation_sample_action|deviation_sample_correction|deviation_system|deviation_system_correction|deviation_system_action|deviation_system_review|zone|section|artefact|mailgroup|tag|document|document_version"})
     * @Security("is_granted('ROLE_SHOW_AUDIT_TRAIL')")
     */
    public function generic(ListGenFactory $listGenFactory, string $category): Response
    {
        $conv = new CamelCaseToSnakeCaseNameConverter(null, false);
        $listClass = $conv->denormalize($category.'_list');
        $list = $listGenFactory->getListGen('App\\ESM\\ListGen\\Admin\\AuditTrail\\'.$listClass);

        return $this->render('admin/auditTrail/generic.html.twig', [
            'list' => $list->getList($category),
            'activeMenu2' => $category,
        ]);
    }

    /**
     * @Route("/{category}/ajax", name="admin.audit_trail.generic.index.ajax",
     *     requirements={"category"="user|profile|project|user_project|funding|publication|service|institution|interlocutor|center|interlocutor_center|date|rule|submission|project_database_freeze|document_tracking|document_tracking_center|document_tracking_interlocutor|courbe_setting|patient|patient_data|visit_patient|drug|document_transverse|version_document_transverse|deviation_correction|deviation|deviation_review|deviation_action|report_visit|deviation_sample|deviation_sample_action|deviation_sample_correction|deviation_system|deviation_system_correction|deviation_system_action|deviation_system_review|zone|section|artefact|mailgroup|tag|document|document_version"})
     * @Security("is_granted('ROLE_SHOW_AUDIT_TRAIL')")
     */
    public function genericAjax(ListGenFactory $listGenFactory, Request $request, string $category): Response
    {
        $conv = new CamelCaseToSnakeCaseNameConverter(null, false);
        $listClass = $conv->denormalize($category.'_list');
        $list = $listGenFactory->getListGen('App\\ESM\\ListGen\\Admin\\AuditTrail\\'.$listClass);
        $list = $list->getList($category);
        $list->setRequestParams($request->query);

        return $list->generateResponse();
    }

    /**
     * @Route("/connexions", name="admin.audit_trail.connexion")
     * @Security("is_granted('ROLE_SHOW_AUDIT_TRAIL')")
     */
    public function conn(ListGenFactory $lgm): Response
    {
        $list = $lgm->getListGen(ConnexionList::class);

        return $this->render('admin/auditTrail/generic.html.twig', [
            'list' => $list->getList(),
            'activeMenu2' => 'connexion',
        ]);
    }

    /**
     * @Route("/connexions/ajax", name="admin.audit_trail.connexion.ajax")
     * @Security("is_granted('ROLE_SHOW_AUDIT_TRAIL')")
     */
    public function connAjax(ListGenFactory $lgm, Request $request): Response
    {
        $list = $lgm->getListGen(ConnexionList::class);
        $list = $list->getList();
        $list->setRequestParams($request->query);

        return $list->generateResponse();
    }

    /**
     * @Route("/connexions-error", name="admin.audit_trail.connexion_error")
     * @Security("is_granted('ROLE_SHOW_AUDIT_TRAIL')")
     */
    public function connError(ListGenFactory $lgm): Response
    {
        $list = $lgm->getListGen(ConnexionErrorList::class);

        return $this->render('admin/auditTrail/generic.html.twig', [
            'list' => $list->getList(),
            'activeMenu2' => 'connexion_error',
        ]);
    }

    /**
     * @Route("/connexions-error/ajax", name="admin.audit_trail.connexion_error.ajax")
     * @Security("is_granted('ROLE_SHOW_AUDIT_TRAIL')")
     */
    public function connErrorAjax(ListGenFactory $lgm, Request $request): Response
    {
        $list = $lgm->getListGen(ConnexionErrorList::class);
        $list = $list->getList();
        $list->setRequestParams($request->query);

        return $list->generateResponse();
    }
}
