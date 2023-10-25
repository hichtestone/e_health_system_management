<?php

namespace App\ESM\Controller\Project;

use App\ESM\Entity\Institution;
use App\ESM\Entity\Project;
use App\ESM\FormHandler\InstitutionHandler;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class InstitutionController
 * @package App\ESM\Controller\Project
 */
class InstitutionController extends AbstractController
{
    /**
     * @Route("/projects/{id}/institution/{idInstitution}", name="project.institution.show", methods="GET", requirements={"id"="\d+", "idInstitution"="\d+"})
     * @ParamConverter("project", options={"id"="id"})
     * @ParamConverter("institution", options={"id"="idInstitution"})
     * @Security("is_granted('PROJECT_ACCESS', project)")
     */
    public function show(Project $project, Institution $institution): Response
    {
    	return $this->render('project/institution/show.html.twig', [
            'institution' => $institution,
            'typeIdNoFiness' => InstitutionHandler::typeIdNoFiness,
        ]);
    }
}
