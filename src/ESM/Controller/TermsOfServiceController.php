<?php

declare(strict_types=1);

namespace App\ESM\Controller;

use App\ESM\Entity\TermsOfServiceSignature;
use App\ESM\Service\TermsOfService\TOSService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TermsOfServiceController.
 */
class TermsOfServiceController extends AbstractController
{
    /**
     * @route("/tos", name="tos")
     * @throws Exception
     */
    public function tos(Request $request, TOSService $TOSService): Response
    {
        $user = $this->getUser();
        $locale = $request->getLocale();

        if ('POST' === $request->getMethod()) {

            if ($TOSService->hasSignedLastTOS($user)) {

                return $this->redirectToRoute('home');

            } else {

                if ('on' !== $request->get('accept')) {

                    $session = $request->getSession();
                    $session->getFlashBag()->add('info', 'tos.please_check');

                    return $this->redirectToRoute('tos');

                } else {

                    $signature = new TermsOfServiceSignature();
                    $signature->setUser($user);
                    $signature->setTermsOfService($TOSService->getLastTOS());
                    $signature->setSignedAt(new \DateTime());

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($signature);
                    $em->flush();

                    return $this->redirectToRoute('home');
                }
            }
        }

        return $this->render('app/tos/tos_'.$locale.'.html.twig', [
            'signed' => $TOSService->hasSignedLastTOS($user),
        ]);
    }
}
