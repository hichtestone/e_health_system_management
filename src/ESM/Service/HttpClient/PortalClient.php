<?php

namespace App\ESM\Service\HttpClient;

use App\ESM\Service\DeviceSession\DeviceSession;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Class PortalClient.
 */
class PortalClient
{
    /** @var string */
    private $portalUrl;
    /** @var HttpClientInterface */
    private $client;
    /** @var string */
    private $bearer;

    /**
     * PortalClient constructor.
     */
    public function __construct(string $portalUrl, string $appSecret, SessionInterface $session, HttpClientInterface $client)
    {
        $this->portalUrl = $portalUrl.('/' === substr($portalUrl, -1) ? '' : '/').'api/';
        $this->bearer = $appSecret.'.'.$session->get(DeviceSession::SESSION_TOKEN_KEY);
        $this->client = $client;
    }

    /**
     * @return ResponseInterface
     *
     * @throws TransportExceptionInterface
     */
    public function request(string $method, string $url, array $options = [])
    {
        $options['auth_bearer'] = $this->bearer;

        return $this->client->request($method, $this->portalUrl.$url, $options);
    }
}
