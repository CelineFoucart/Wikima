<?php

namespace App\Security\Voter;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class PostVoter extends Voter
{
    public function __construct(
        private VoterHelper $voterHelper,
        private PostRepository $postRepository
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [VoterHelper::EDIT, VoterHelper::DELETE, VoterHelper::VIEW])
            && $subject instanceof \App\Entity\Post;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case VoterHelper::EDIT:
                return $this->candEdit($user, $subject);
                break;
            case VoterHelper::VIEW:
                return $this->voterHelper->canModerateForum($user);
                break;
            case VoterHelper::DELETE:
                return $this->canDelete($user, $subject);
                break;
        }

        return false;
    }

    public function candEdit(User $user, Post $post): bool
    {
        if ($this->voterHelper->canModerateForum($user)) {
            return true;
        }

        return $this->voterHelper->canEdit($user, $post, false);
    }

    public function canDelete(User $user, Post $post)
    {
        if ($this->voterHelper->canModerateForum($user)) {
            return true;
        }

        $firstPost = $this->postRepository->findFirstPost($post->getTopic()->getId());
        
        if ($firstPost[0]->getId() === $post->getId()) {
            return false;
        }

        return $this->voterHelper->canEdit($user, $post, false);
    }
}
