<?php

declare(strict_types=1);

namespace App\ESM\Controller\Security;

use App\ESM\Entity\User;
use App\ESM\Form\Security\TwoFactorType;
use App\ESM\Service\OTP\OTPService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * Class TwoFactorController.
 */
class TwoFactorController extends AbstractController
{
    /**
     * @Route("/two-factor", name="two_factor")
     */
    public function twoFactor(Request $request, TokenStorageInterface $tokenStorage, OTPService $OTPService): Response
    {
        // if already auth => home
        /** @var PostAuthenticationGuardToken $currentToken */
        $currentToken = $tokenStorage->getToken();
        /** @var User $user */
        $user = $this->getUser();

        if (in_array(OTPService::ROLE_TWO_FACTOR_SUCCEED, $currentToken->getAttributes()['cust_roles'] ?? [], true)) {
            return $this->redirectToRoute('home');
        }

        // create OTP from user
        $otp = $OTPService->getUserOTP($user);

        // create form
        $form = $this->createForm(TwoFactorType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // check code
            $code = $form->get('code')->getData();

            if (!$otp->verify($code)) {
                $form->get('code')->addError(new FormError('security.connexion.err.two_factor_bad_code'));
            } else {
                $OTPService->addTwoFactorRole($tokenStorage, $request->getSession());

                return $this->redirectToRoute('home');
            }
        }

        // reset field
        $formView = $form->createView();
        $formView->children['code']->vars['value'] = '';

        // view
        return $this->render('security/double_factor.html.twig', [
            'form' => $formView,
        ]);
    }

    /**
     * @route("/two-factor/enable", name="two_factor.enable")
     *
     * @throws \Exception
     */
    public function enable(Request $request, OTPService $OTPService): Response
    {
        // get user
        /** @var User $user */
        $user = $this->getUser();

        // create form
        $form = $this->createForm(TwoFactorType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // create OTP from session
            $secret = $request->getSession()->get('otp_secret');
            $otp = $OTPService->generateNewOtp($user, $secret);

            // check code
            $code = $form->get('code')->getData();

            if (!$otp->verify($code)) {
                $form->get('code')->addError(new FormError('security.connexion.err.two_factor_bad_code'));
            } else {
                // store secret in user
                $user->setTwoFactorSecret($secret);

                // persist
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                // clean up session
                $request->getSession()->remove('otp_secret');

                // redirect
                /** @var Session $session */
                $session = $request->getSession();
                $session->getFlashBag()->add('success', 'security.two_factor.msg.enabled');

                return $this->redirectToRoute('account');
            }
        } else {
            // generate secret and store it in session
            $secret = $OTPService->generateSecret();
            $request->getSession()->set('otp_secret', $secret);
        }

        // reset field
        $formView = $form->createView();
        $formView->children['code']->vars['value'] = '';

        // view
        $otp = $OTPService->generateNewOtp($user, $secret);

        return $this->render('security/double_factor_test.html.twig', [
            'otpUrl' => $otp->getProvisioningUri(),
            'form' => $formView,
        ]);
    }

    /**
     * @route("/two-factor/disable", name="two_factor.disable")
     */
    public function disable(Request $request): Response
    {
        // disable two-factor auth
        /** @var User $user */
        $user = $this->getUser();
        $user->setTwoFactorSecret(null);

        // persist
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        // view
        /** @var Session $session */
        $session = $request->getSession();
        $session->getFlashBag()->add('success', 'security.two_factor.msg.disabled');

        return $this->redirectToRoute('account');
    }
}
