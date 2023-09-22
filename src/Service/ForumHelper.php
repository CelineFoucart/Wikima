<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\ForumGroupRepository;

final class ForumHelper
{
    public function __construct(private ForumGroupRepository $forumGroupRepository)
    {
        
    }

    /**
     * @return ForumGroup[]
     */
    public function getCurrentUserRoles(?User $user): array
    {
        $anonymous = $this->forumGroupRepository->findByRoleName(['roleName' => 'PUBLIC_ACCESS']);
        
        if (!$user instanceof User) {
            return $anonymous;
        }

        $userGroups = $user->getUserGroups();
        if ($userGroups->isEmpty()) {
            return $anonymous;
        }

        $groups = [];

        foreach ($userGroups as $userGroup) {
            $groups[] = $userGroup->getForumGroup();
        }

        return $groups;
    }
}
