<?php

declare(strict_types=1);

namespace App\ESM\Controller\Security;

use App\ESM\Entity\Application;
use App\ESM\Service\DeviceSession\DeviceSession;
use App\ESM\Service\SingleSignOn\SSOService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SSOController.
 */
class SSOController extends AbstractController
{
    /**
     * test si session ouverte.
     *
     * @Route("/sso/acs", name="sso.acs", methods={"GET"})
     */
    public function setToken(Request $request, SSOService $SSOService): Response
    {
        // si pas de service provider, on n'a rien à faire là
        $targetUrl = $request->get(SSOService::GET_SP_PARAM);
        $spUrl = $SSOService->getDomain($targetUrl);
        if (null === $spUrl) {
            $request->getSession()->getFlashBag()->add('warning', 'Bad request');
            return $this->redirectToRoute('login');
        }

        // on check application existe
        $appRep = $this->getDoctrine()->getRepository(Application::class);
        $application = $appRep->findOneBy(['url' => $spUrl]);
        if (null === $application) {
            $request->getSession()->getFlashBag()->add('warning', 'Unknown service provider: '.$spUrl);
            return $this->redirectToRoute('login');
        }

        // si a accès au sp
        if ($appRep->hasUserAccess($this->getUser(), $application)) {
            return $this->render('security/acs.html.twig', [
                'target' => $targetUrl,
                'fields' => [
                    'app_id' => $application->getId(),
                    'session_token' => $request->getSession()->get(DeviceSession::SESSION_TOKEN_KEY),
                ],
            ]);
        } else {
            $request->getSession()->getFlashBag()->add('warning', 'Unknown service provider: '.$spUrl);
            return $this->redirectToRoute('login');
        }
    }
}
