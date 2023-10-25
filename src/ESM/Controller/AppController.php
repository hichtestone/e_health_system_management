<?php

namespace App\ESM\Controller;

use App\ESM\Service\DeviceSession\DeviceSession;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AppController
 * @package App\ESM\Controller
 */
class AppController extends AbstractController
{
	/**
	 * @Route("/releases", name="app.release_notes")
	 * @return Response
	 */
    public function releaseNote(): Response
	{
        return $this->render('app/release_notes.html.twig', []);
    }

    /**
     * @Route("/help", name="app.help")
     */
    public function help(): Response
    {
        return $this->render('app/help.html.twig', []);
    }

    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->redirectToRoute('projects.index');
    }

    /**
     * @route("/ping", name="ping")
     */
    public function ping(Request $request, DeviceSession $deviceSession): JsonResponse
    {
        $sessionToken = $request->getSession()->get(DeviceSession::SESSION_TOKEN_KEY);
        $connexion = $deviceSession->getConnexion($sessionToken);

        if (null !== $connexion && !$deviceSession->isExpiredConnexion($connexion)) {
            $connexion->setLastPing(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($connexion);
            $em->flush();
            return $this->json(['status' => 1, 'msg' => 'pong']);
        } else {
            $deviceSession->destroy($sessionToken, 2);
            $request->getSession()->invalidate(1);

            return $this->json(['status' => 0, 'msg' => 'error']);
        }
    }

    /**
     * @Route("/public/ajax/popup/support", name="popup.support")
     */
    public function ajaxPopupSupport(): Response
    {
        return $this->render('app/popup_support.html.twig');
    }

    /**
     * @Route("/account", name="account")
     */
    public function account(): Response
    {
        return $this->render('app/account.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @route("/language/{code}/select", name="language.select", requirements={"code"="[a-z]{2}"})
     */
    public function lang(string $code, Request $request): Response
    {
        $referer = $request->headers->get('referer');
        $response = new RedirectResponse($referer, 307);
        if (in_array($code, ['en', 'fr'])) {
            $request->getSession()->set('_locale', $code);
            $response->headers->setCookie(Cookie::create('language', $code, time() + (24 * 3600 * 90), '/')); // 3 mois

            if (null !== $this->getUser()) {
                $this->getUser()->setLocale($code);
                $this->getDoctrine()->getManager()->persist($this->getUser());
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return $response;
    }

    /**
     * @route("/admin", name="admin.index")
     */
    public function admin(): Response
    {
        $routes = [
            'PROJECT_LIST' => 'admin.projects.index',
            'USER_LIST' => 'admin.user.index',
            'PROFILE_LIST' => 'admin.profiles.index',
            'SITE_LIST' => 'admin.sites.index',
            'INSTITUTION_LIST' => 'admin.institutions.index',
            'INTERLOCUTOR_LIST' => 'admin.interlocutors.index',
            'DRUG_LIST' => 'admin.drugs.index',
            'CENTER_LIST' => 'project.center.index',
            'PROJECTINTERLOCUTOR_LIST' => 'project.list.interlocutors',
            'MONITORING_MODEL_LIST' => 'admin.monitoring_model.index',
            'ROLE_SHOW_AUDIT_TRAIL' => ['admin.audit_trail.generic.index', ['category' => 'project']],
        ];

        foreach ($routes as $role => $path) {
            if ($this->isGranted($role)) {
                if (is_array($path)) {
                    return $this->redirectToRoute($path[0], $path[1]);
                } else {
                    return $this->redirectToRoute($path);
                }
            }
        }

        return $this->redirectToRoute('home');
    }
}
