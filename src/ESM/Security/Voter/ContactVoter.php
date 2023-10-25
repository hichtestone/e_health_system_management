<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Contact;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ContactVoter extends Voter
{
    const LIST = 'CONTACT_LIST';
    const SHOW = 'CONTACT_SHOW';
    const EDIT = 'CONTACT_EDIT';
    const CREATE = 'CONTACT_CREATE';
    const DELETE = 'CONTACT_DELETE';

    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, [self::LIST, self::CREATE], true) ||
            (in_array($attribute, [self::SHOW, self::EDIT, self::DELETE], true)
                && $subject instanceof Contact);
    }

    protected function voteOnAttribute($attribute, $contact, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();

            case self::SHOW:
                return $this->canShow($contact);

            case self::EDIT:
                return $this->canEdit($contact);

            case self::CREATE:
                return $this->canCreate();

            case self::DELETE:
                return $this->canDelete($contact);
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_COMMUNICATION_READ');
    }

    private function canShow(Contact $contact): bool
    {
        return $this->security->isGranted('ROLE_COMMUNICATION_READ');
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_COMMUNICATION_WRITE');
    }

    private function canEdit(Contact $contact): bool
    {
        return $this->security->isGranted('ROLE_COMMUNICATION_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $contact->getProject());
    }

    private function canDelete(Contact $contact): bool
    {
        return $this->security->isGranted('ROLE_COMMUNICATION_DELETE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $contact->getProject());
    }
}
