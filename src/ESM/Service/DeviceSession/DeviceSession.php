<?php

declare(strict_types=1);

namespace App\ESM\Service\DeviceSession;

use App\ESM\Entity\Application;
use App\ESM\Entity\ConnectionAuditTrail;
use App\ESM\Entity\User;
use App\ESM\Repository\ConnectionAuditTrailRepository;
use App\ESM\Service\Redis\RedisClient;
use DateTime;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * session partagée entre toutes les applis Clinfile permettant une connexion multi device
 * Class DeviceSession.
 */
class DeviceSession
{
    /** @var string */
    public const SESSION_TOKEN_KEY = 'device_session_token';

    private const prefix = 'DST_';

    /** @var EntityManagerInterface */
    private $em;
    /** @var RedisClient */
    private $redis;
    /** @var SessionInterface */
    private $session;
    /** @var Request */
    private $request;
    /** @var string */
    private $deviceSessionToken;
    /** @var int */
    private $timeout;
    /** @var int */
    private $timeping;

    /**
     * DeviceSession constructor.
     */
    public function __construct(int $timeout, int $timeping, EntityManagerInterface $em, RedisClient $redis, SessionInterface $session, RequestStack $request)
    {
        $this->timeout = $timeout;
        $this->timeping = $timeping;
        $this->em = $em;
        $this->redis = $redis;
        $this->request = $request->getCurrentRequest();
        $this->session = $session;
    }

    /**
     * check si session existe et n'est pas expirée.
     */
    public function deviceSessionExists(string $deviceSessionToken): bool
    {
        return null !== $this->getConnexion($deviceSessionToken);
        //return false !== $this->redis->get($deviceSessionToken);
    }

    /**
     * check si session existe et n'est pas expirée.
     */
    public function getConnexion(string $deviceSessionToken): ?ConnectionAuditTrail
    {
        return $this->em->getRepository(ConnectionAuditTrail::class)
            ->findOneBy([
                'device_session_token' => $deviceSessionToken,
                'ended_at' => null,
            ]);
    }

    public function isExpiredConnexion(ConnectionAuditTrail $connexion): bool
    {
        return (time() - $connexion->getLastRefresh()->getTimeStamp()) > $this->timeout;
    }

    public function refreshTimeout(ConnectionAuditTrail $connexion): void
    {
        $connexion->setLastRefresh(new DateTime());

        $this->em->persist($connexion);
        $this->em->flush();
    }

    /**
     * create device session when does'nt exist.
     *
     * @throws \Exception
     */
    public function createSession(User $user): void
    {
        // get portal
        $portal = $this->em->getRepository(Application::class)->find(1);

        // generate device token
        $this->deviceSessionToken = $this->generateToken();

        // set session
        $this->session->set(self::SESSION_TOKEN_KEY, $this->deviceSessionToken);
        $this->session->set('this_app', $portal->getId());

        // set session token in db
        $this->logConnection($user, $portal);

        // redis
        //$this->initRedis($user, $portal);
    }

    /**
     * @throws DBALException
     */
    public function destroy(string $deviceSessionToken, int $reason): void
    {
        // destroy redis entry
        //$this->redis->delete($deviceSessionToken);

        // destroy in session
        $this->session->remove(DeviceSession::SESSION_TOKEN_KEY);

        // log logout
        /** @var ConnectionAuditTrailRepository $repConnAT */
        $repConnAT = $this->em->getRepository(ConnectionAuditTrail::class);
        $repConnAT->logoutDevice($deviceSessionToken, $reason);
    }

    private function initRedis(User $user, Application $portal): void
    {
        $this->redis->set($this->deviceSessionToken, [
            'startedAt' => date('U'),
            'deviceToken' => $this->deviceSessionToken,
            'portalId' => $portal->getId(),
            'userAgent' => $this->request->headers->get('user-agent'),
            'userId' => $user->getId(),
        ]);
    }

    /**
     * @throws \Exception
     */
    private function generateToken(): string
    {
        return self::prefix.md5(random_bytes(64));
    }

    public function closeOldConnections(): void
    {
        $this->em->getRepository(ConnectionAuditTrail::class)->closeOldConnections($this->timeout);
    }

    public function hasBeenPinged(ConnectionAuditTrail $conn): bool
    {
        return (time() - $conn->getLastPing()->getTimeStamp()) < $this->timeping + 1;
    }

    private function logConnection(User $user, Application $app): void
    {
        $log = new ConnectionAuditTrail();
        $log->setUser($user);
        $log->setStartedAt(new DateTime());
        $log->setLastPing(new DateTime());
        $log->setLastRefresh(new DateTime());
        $log->setApplication($app);
        $log->setIp($this->request->getClientIp());
        $log->setUseragent($this->request->headers->get('user-agent'));
        $log->setDeviceSessionToken($this->deviceSessionToken);

        $this->em->persist($log);
        $this->em->flush();
    }
}
