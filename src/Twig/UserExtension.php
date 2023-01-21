<?php

namespace App\Twig;

use App\Service\UserService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserExtension extends AbstractExtension
{

    public function __construct(
        private UserService $userService
    ) { }
    
    public function getFunctions(): array
    {
        return [
            new TwigFunction('format_roles', [$this, 'getFormattedRoles']),
        ];
    }
    
    public function getFormattedRoles(array $roles): string
    {

        return $this->userService->formatRoles($roles);
    }
}
