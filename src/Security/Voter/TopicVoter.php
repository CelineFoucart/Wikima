<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Topic;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TopicVoter extends Voter
{
    public function __construct(
        private VoterHelper $voterHelper
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [VoterHelper::EDIT, VoterHelper::DELETE, VoterHelper::CREATE])
            && $subject instanceof \App\Entity\Topic;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case VoterHelper::EDIT:
                return $this->voterHelper->canModerate($user, $subject, true);
                break;
            case VoterHelper::CREATE:
                return $this->canCreate($user, $subject);
                break;
            case VoterHelper::DELETE:
                return $this->voterHelper->canModerate($user, $subject, true);
                break;
        }

        return false;
    }

    private function canCreate(User $user, Topic $subject): bool
    {
        if ($this->voterHelper->canModerateForum($user, $subject, true)) {
            return true;
        }

        return $subject->isLocked() === false;
    }
}
