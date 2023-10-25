<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Institution;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DocumentTransverseInstitutionVoter extends Voter
{
    const LIST = 'INSTITUTION_LIST_DOCUMENTTRANSVERSE';
    const INSTITUTION = 'INSTITUTION_READ_WRITE_ARCHIVE_DOCUMENTTRANSVERSE';
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
        return in_array($attribute, [self::INSTITUTION, self::LIST], true)
            && $subject instanceof Institution;
    }

    protected function voteOnAttribute($attribute, $institution, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canListDocumentTransverseInstitution($institution);
            case self::INSTITUTION:
                return $this->canCreateDocumentTransverseInstitution($institution);
        }

        return false;
    }

    private function canListDocumentTransverseInstitution(Institution $institution): bool
    {
        return ($this->security->isGranted('ROLE_INSTITUTION_READ') || $this->security->isGranted('ROLE_CENTER_READ')) && null == $institution->getDeletedAt() && $this->security->isGranted('ROLE_DOCUMENTTRANSVERSE_READ');
    }

    private function canCreateDocumentTransverseInstitution(Institution $institution): bool
    {
        return $this->security->isGranted('ROLE_INSTITUTION_READ') && null == $institution->getDeletedAt() && $this->security->isGranted('ROLE_INSTITUTION_READ_WRITE_ARCHIVE_DOCUMENTTRANSVERSE');
    }
}
