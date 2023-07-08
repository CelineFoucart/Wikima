<?php

namespace App\Security\Voter;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;

class VoterHelper
{
    public const EDIT = 'edit';

    public const DELETE = 'delete';

    public const VIEW = 'view';

    public const CREATE = 'create';

    public function canModerate(User $user): bool
    {
        return in_array("ROLE_ADMIN", $user->getRoles()) || in_array("ROLE_SUPER_ADMIN", $user->getRoles());
    }

    public function isEditor(User $user): bool
    {
        return in_array("ROLE_EDITOR", $user->getRoles()) || $this->canModerate($user);
    }

    /**
     * @param User $user
     * @param Comment|Article $subject
     * 
     * @return bool
     */
    public function canEdit(User $user, $subject, $forEditor = false): bool
    {
        if ($this->canModerate($user)) {
            return true;
        }
        
        $isAuthor = $user->getId() === $subject->getAuthor()->getId();

        if ($forEditor) {
            return $isAuthor && $this->isEditor($user);
        }

        return $isAuthor;
    }

    /**
     * @param User $user
     * @param Comment|Article $subject
     * 
     * @return bool
     */
    public function canDelete(User $user, $subject, $forEditor = false): bool
    {
        return $this->canEdit($user, $subject, $forEditor);
    }
}