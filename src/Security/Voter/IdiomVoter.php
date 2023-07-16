<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class IdiomVoter extends Voter
{
    public function __construct(
        private VoterHelper $voterHelper
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [VoterHelper::EDIT, VoterHelper::DELETE, VoterHelper::CREATE])
            && $subject instanceof \App\Entity\Idiom;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case VoterHelper::EDIT:
                return $this->voterHelper->canEdit($user, $subject, true);
                break;
            case VoterHelper::CREATE:
                return $this->voterHelper->isEditor($user);
                break;
            case VoterHelper::DELETE:
                return $this->voterHelper->canDelete($user, $subject, true);
                break;
        }

        return false;
    }
}
