<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Drug;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DocumentTransverseDrugVoter extends Voter
{
    const LIST = 'DRUG_LIST_DOCUMENTTRANSVERSE';
    const DRUG = 'DRUG_READ_WRITE_ARCHIVE_DOCUMENTTRANSVERSE';
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
        return in_array($attribute, [self::DRUG, self::LIST], true)
            && $subject instanceof Drug;
    }

    protected function voteOnAttribute($attribute, $drug, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canListDocumentTransverseDrug($drug);
            case self::DRUG:
                return $this->canCreateDocumentTransverseDrug($drug);
        }

        return false;
    }

    private function canListDocumentTransverseDrug(Drug $drug): bool
    {
        return ($this->security->isGranted('ROLE_DRUG_READ') || $this->security->isGranted('ROLE_PROJECT_SETTINGS_READ'))
			&& null == $drug->getDeletedAt() && $this->security->isGranted('ROLE_DOCUMENTTRANSVERSE_READ');
    }

    private function canCreateDocumentTransverseDrug(Drug $drug): bool
    {
        return $this->security->isGranted('ROLE_DRUG_READ') && null == $drug->getDeletedAt() && $this->security->isGranted('ROLE_DRUG_READ_WRITE_ARCHIVE_DOCUMENTTRANSVERSE');
    }
}
