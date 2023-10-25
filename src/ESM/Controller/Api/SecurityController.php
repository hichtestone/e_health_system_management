<?php

declare(strict_types=1);

namespace App\ESM\Controller\Api;

use App\ESM\Entity\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AppController.
 */
class SecurityController extends AbstractController
{
    private $privateKey;

    /**
     * SecurityController constructor.
     */
    public function __construct(string $jwt_private_key)
    {
        $this->privateKey = $jwt_private_key;
    }

    /**
     * @Route("/api/get-token", name="api.get_token", methods={"POST"})
     */
    public function getToken(Request $request)
    {
        // check valid query
        $userId = $request->get('user_id');
        $apiToken = $request->get('api_token');
        if (null === $userId || null === $apiToken || !is_numeric($request->get('user_id'))) {
            return $this->json([
                'error' => 'Bad request',
            ]);
        } else {
            $userId = (int) $userId;
            // check user can access app
            if ($this->getDoctrine()->getRepository(Application::class)
                ->canGiveToken($apiToken, $userId)) {
                // generate jwt
                $payload = [
                    'user_portal_id' => $userId,
                    'expired_at' => (new \DateTime('now +15 minute'))->format('Y-m-d H:i:s'),
                ];
                $jwt = JWT::encode($payload, $this->privateKey, 'RS256');

                return $this->json([
                    'type' => 'bearer',
                    'token' => $jwt,
                ]);
            } else {
                return $this->json([
                    'error' => 'Unauthorised',
                ]);
            }
        }
    }
}
