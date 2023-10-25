<?php

namespace App\ESM\Controller;

use App\ESM\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/{userID}/job", name="user.get.job.xhr", methods={"GET", "POST"}, requirements={"userID"="\d+"}, options={"expose"=true})
     * @ParamConverter("user", options={"id"="userID"})
     * @return Response
     */
    public function getUserJob(Request $request, User $user)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedException();
        }

        try {
            if (!$userJob = $user->getJob()) {
                return new JsonResponse(['jobLabel' => '', 'statusLabel' => 'KO', 'errorMessage' => 'User does not have a job'], 500);
            }

            $userJobLabel = $userJob->getLabel();

            if ($userJobLabel === '' || $userJobLabel === null) {
                return new JsonResponse(['jobLabel' => '', 'statusLabel' => 'KO', 'errorMessage' => 'the job does not have a label'], 500);
            }

            $status = 200;
            $statusLabel = 'OK';
            $errorMessage = '';

        } catch (\Exception $exception) {

            $status = 500;
            $statusLabel = 'KO';
            $userJobLabel = '';
            $errorMessage = $exception->getMessage();
        }

        return new JsonResponse(['jobLabel' => $userJobLabel, 'statusLabel' => $statusLabel, 'errorMessage' => $errorMessage], $status);
    }
}
