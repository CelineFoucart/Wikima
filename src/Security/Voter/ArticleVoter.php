<?php

namespace App\Security\Voter;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ArticleVoter extends Voter
{
    public function __construct(
        private VoterHelper $voterHelper
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [VoterHelper::EDIT, VoterHelper::DELETE, VoterHelper::CREATE, VoterHelper::VIEW])
            && $subject instanceof \App\Entity\Article;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

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
            case VoterHelper::VIEW:
                return $this->canView($user, $subject);
        }

        return false;
    }

    protected function canView(?User $user, Article $article): bool
    {
        if ($this->voterHelper->canEdit($user, $article, true)) {
            return true;
        }

        return true !== $article->getIsDraft() && true !== $article->getIsPrivate() && true !== $article->getIsArchived();
    }
}
