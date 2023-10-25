<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Center;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/institution")
 */
class InstitutionController extends AbstractController
{
    /**
     * @Route("/get/{centerID}", name="institution.get.xhr", methods="GET", requirements={"centerID"="\d+"}, options={"expose"=true})
     * @ParamConverter("center", options={"id"="centerID"})
     * @return Response
     */
    public function getInstitutions(Request $request, Center $center, SerializerInterface $serializer)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedException();
        }

        try {
            if (!$institutions = $center->getInstitutions()) {
                return new JsonResponse(['institutions' => '', 'statusLabel' => 'KO', 'errorMessage' => 'Center does not have institutions'], 500);
            }

            $status       = 200;
            $statusLabel  = 'OK';
            $errorMessage = '';
            $institutions = $serializer->serialize($institutions, 'json', ['ignored_attributes' => ['country', 'countryDepartment', 'institutionType', 'centers', 'deviations', 'documentTransverses', 'interlocutors', 'services']]);

        } catch (\Exception $exception) {

            $status       = 500;
            $statusLabel  = 'KO';
            $errorMessage = $exception->getMessage();
            $institutions = '';
        }

        return new JsonResponse(['institutions' => $institutions, 'statusLabel' => $statusLabel, 'errorMessage' => $errorMessage], $status);
    }
}
