<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Interlocutor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DocumentTransverseInterlocutorVoter extends Voter
{
    const LIST = 'INTERLOCUTOR_LIST_DOCUMENTTRANSVERSE';
    const INTERLOCUTOR = 'INTERLOCUTOR_READ_WRITE_ARCHIVE_DOCUMENTTRANSVERSE';
    /**
     * @var Security
     */
    private $security;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->em = $em;
    }

    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, [self::INTERLOCUTOR, self::LIST], true)
            && $subject instanceof Interlocutor;
    }

    protected function voteOnAttribute($attribute, $interlocutor, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canListDocumentTransverseInterlocutor($interlocutor);
            case self::INTERLOCUTOR:
                return $this->canCreateDocumentTransverseInterlocutor($interlocutor);
        }

        return false;
    }

    private function canListDocumentTransverseInterlocutor(Interlocutor $interlocutor): bool
    {
        return ($this->security->isGranted('ROLE_INTERLOCUTOR_READ') || $this->security->isGranted('ROLE_PROJECT_INTERLOCUTOR_READ')) && null == $interlocutor->getDeletedAt() && $this->security->isGranted('ROLE_DOCUMENTTRANSVERSE_READ');
    }

    private function canCreateDocumentTransverseInterlocutor(Interlocutor $interlocutor): bool
    {
        return $this->security->isGranted('ROLE_INTERLOCUTOR_READ') && null == $interlocutor->getDeletedAt() && $this->security->isGranted('ROLE_INTERLOCUTOR_READ_WRITE_ARCHIVE_DOCUMENTTRANSVERSE');
    }
}
