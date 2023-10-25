<?php

declare(strict_types=1);

namespace App\ESM\Controller\Security;

use App\ESM\Form\Security\LoginType;
use App\ESM\Service\SingleSignOn\SSOService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AppController.
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login", methods={"GET"})
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils, TranslatorInterface $translator): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // si pas de sp
        if (null === $request->get(SSOService::GET_SP_PARAM)) {
            $request->getSession()->remove(SSOService::SESSION_SP_URL_KEY);
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // create form
        $form = $this->createForm(LoginType::class, null, []);

        // set error
        if (null !== $error) {
            $form->addError(new FormError($translator->trans($error->getMessageKey())));
        }

        // set username data
        if (null !== $lastUsername) {
            $form->get('email')->setData($lastUsername);
        }

        // view
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/logincheck", name="login.check", methods={"POST"})
     */
    public function loginCheck(): void
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('login check in security.yaml undefined');
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     */
    public function logout(): void
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
