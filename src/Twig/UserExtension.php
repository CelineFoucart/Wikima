<?php

namespace App\Twig;

use App\Entity\User;
use App\Security\Voter\VoterHelper;
use Twig\TwigFunction;
use App\Service\UserService;
use Twig\Extension\AbstractExtension;

class UserExtension extends AbstractExtension
{

    public function __construct(
        private UserService $userService,
        private VoterHelper $voterHelper
    ) { }
    
    public function getFunctions(): array
    {
        return [
            new TwigFunction('format_roles', [$this, 'getFormattedRoles']),
            new TwigFunction('can_access', [$this, 'canAccess']),
        ];
    }
    
    public function getFormattedRoles(array $roles): string
    {

        return $this->userService->formatRoles($roles);
    }

    public function canAccess(?User $currentUser, $subject): bool
    {
        return $this->voterHelper->canEdit($currentUser, $subject, true);
    }
}
