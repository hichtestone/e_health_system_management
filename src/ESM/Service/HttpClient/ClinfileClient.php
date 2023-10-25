<?php

namespace App\ESM\Service\HttpClient;

use App\ESM\Entity\Application;
use App\ESM\Entity\User;
use Exception;
use Firebase\JWT\JWT;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * client compatible avec les applications Clinfile.
 */
class ClinfileClient
{
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var HttpClientInterface
     */
    private $client;
    /**
     * @var string
     */
    private $privateKey;
    /**
     * @var string
     */
    private $jwt;
    /**
     * @var ResponseInterface
     */
    private $response;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var string
     */
    private $endpoint;
    /**
     * @var User
     */
    private $user;

    public function __construct(string $jwt_private_key, SessionInterface $session, HttpClientInterface $client, LoggerInterface $logger)
    {
        $this->session = $session;
        $this->client = $client;
        $this->privateKey = $jwt_private_key;
        $this->logger = $logger;
    }

    /**
     * génère un token pour un user.
     */
    public function generateJWT(User $user): void
    {
        $this->user = $user;
        $payload = [
            'user_portal_id' => $user->getId(),
            'expired_at' => (new \DateTime('now +15 minute'))->format('Y-m-d H:i:s'),
        ];
        $this->jwt = JWT::encode($payload, $this->privateKey, 'RS256');
    }

    /**
     * @return int
     *
     * @throws Exception
     * @throws TransportExceptionInterface
     */
    public function request(Application $application, string $method, string $url, array $options = [])
    {
        if (null === $this->jwt) {
            throw new Exception('JWT undefined');
        }
        $options['auth_bearer'] = $this->jwt;
        $options['headers']['Auth-Clinfile'] = $this->jwt; // auth_bearer disparait chez Cegedim...
        $this->endpoint = $application->getUrl().$url;

        try {
            $this->response = $this->client->request($method, $this->endpoint, $options);

            return $this->response->getStatusCode();
        } catch (Exception $e) {
            $errorMsg = 'Unexpected API response for endpoint "'.$this->endpoint.'" with user '.$this->user->getId();
            $this->logger->error($errorMsg, [$this->client]);
            throw $e;
        }
    }

    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return ResponseInterface
     *
     * @throws TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function getJson()
    {
        $text = $this->response->getContent(false);
        $json = json_decode($text, true);
        if (!is_array($json) || !array_key_exists('status', $json)) {
            $errorMsg = 'Unexpected error';
            if (!is_array($json)) {
                $errorMsg = 'Unexpected API response for endpoint "'.$this->endpoint.'" with user '.$this->user->getId().'. Bad json format: "'.$text.'"';
            } elseif (!array_key_exists('status', $json)) {
                $errorMsg = 'Unexpected API response for endpoint "'.$this->endpoint.'" with user '.$this->user->getId().'. No "status" key found: "'.$text.'"';
            }
            $this->logger->error($errorMsg, [$json, $text]);
            throw new Exception($errorMsg);
        }

        return $json;
    }
}
