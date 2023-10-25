<?php

declare(strict_types=1);

namespace App\ESM\Security;

use App\ESM\Entity\ConnectionAuditTrail;
use App\ESM\Entity\ConnectionErrorAuditTrail;
use App\ESM\Entity\User;
use App\ESM\Security\Exception\AccountDisabledException;
use App\ESM\Security\Exception\AccountLockedException;
use App\ESM\Security\Exception\AlreadyLoggedInException;
use App\ESM\Service\DeviceSession\DeviceSession;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserChecker.
 */
class UserChecker implements UserCheckerInterface
{
    private $em;
    private $request;
    private $deviceSession;

    /**
     * UserChecker constructor.
     */
    public function __construct(EntityManagerInterface $em, RequestStack $requestStack, DeviceSession $deviceSession)
    {
        $this->em = $em;
        $this->request = $requestStack->getCurrentRequest();
        $this->deviceSession = $deviceSession;
    }

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        // user blocked
        $connErrorRep = $this->em->getRepository(ConnectionErrorAuditTrail::class);
        if ($connErrorRep->getNbFailedConnexion($user, 300) >= 5) {
            $err = new AccountLockedException();
            $this->logError($user, $err->getMessage());
            throw $err;
        }

        // user is deleted, show a generic Account Not Found message.
        if (null !== $user->getDeletedAt() || !$user->getHasAccessESM()) {
            $err = new AccountDisabledException();
            $this->logError($user, $err->getMessage());
            throw $err;
        }

        // user already connected
        $nbConnMax = 1;
        $this->deviceSession->closeOldConnections();
        $connRep = $this->em->getRepository(ConnectionAuditTrail::class);
        $conn = $connRep->findBy(['user' => $user, 'ended_at' => null]);
        if (count($conn) >= $nbConnMax) {
            // on check si une des connexions n'a pas été pinguée normalement on la remplace
            foreach ($conn as $cnn) {
                if (!$this->deviceSession->hasBeenPinged($cnn)) {
                    $cnn->setEndedAt(new \DateTime());
                    $cnn->setReason(3);
                    $this->em->persist($cnn);
                    $this->em->flush();

                    return;
                }
            }

            $err = new AlreadyLoggedInException();
            $this->logError($user, $err->getMessage());
            throw $err;
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }
    }

    private function logError(User $user, string $msg): void
    {
        $error = new ConnectionErrorAuditTrail();
        $error->setCreatedAt(new \DateTime());
        $error->setUser($user);
        $error->setError($msg);
        $error->setIp($this->request->getClientIp());
        $error->setDevice($this->request->headers->get('User-Agent'));
        $this->em->persist($error);
        $this->em->flush();
    }
}
