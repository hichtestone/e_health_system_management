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
 * @Route("/patient")
 */
class PatientController extends AbstractController
{
    /**
     * @Route("/get/{centerID}", name="patient.get.xhr", methods="GET", requirements={"centerID"="\d+"}, options={"expose"=true})
     * @ParamConverter("center", options={"id"="centerID"})
     * @return Response
     */
    public function getPatients(Request $request, Center $center, SerializerInterface $serializer)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedException();
        }

        try {
            if (!$patients = $center->getPatients()) {
                return new JsonResponse(['patients' => '', 'statusLabel' => 'KO', 'errorMessage' => 'Center does not have patient'], 500);
            }

            $status       = 200;
            $statusLabel  = 'OK';
            $errorMessage = '';
            $patients     = $serializer->serialize($patients, 'json', ['groups' => ['center-patient']]);

        } catch (\Exception $exception) {

            $status       = 500;
            $statusLabel  = 'KO';
            $errorMessage = $exception->getMessage();
            $patients     = '';
        }

        return new JsonResponse(['patients' => $patients, 'statusLabel' => $statusLabel, 'errorMessage' => $errorMessage], $status);
    }
}
