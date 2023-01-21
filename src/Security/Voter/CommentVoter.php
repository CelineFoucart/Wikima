<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    public function __construct(
        private VoterHelper $voterHelper
    ) {  }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [VoterHelper::EDIT, VoterHelper::DELETE])
            && $subject instanceof \App\Entity\Comment;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case VoterHelper::EDIT:
                return $this->voterHelper->canEdit($user, $subject);
                break;
            case VoterHelper::DELETE:
                return $this->voterHelper->canDelete($user, $subject);
                break;
        }

        return false;
    }
}
