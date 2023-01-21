<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class UserService
{
    private array $availableRoles = [
        'User' => 'ROLE_USER',
        'Editor' => 'ROLE_EDITOR',
        'Administrator' => 'ROLE_ADMIN',
    ];

    public function __construct(
        private TranslatorInterface $translator,
        private TokenStorageInterface $tokenStorage
    ) {
    }

    /**
     * Get a list of available roles.
     */
    public function getAvailableRoles(): array
    {
        $token = $this->tokenStorage->getToken();
        $user = ($token) ? $token->getUser() : null;

        if ($user instanceof User && in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            $this->availableRoles['Founder Administrator'] = 'ROLE_SUPER_ADMIN';
        }

        return $this->availableRoles;
    }

    public function formatRoles(array $roles): string
    {
        $userRoles = [];

        foreach ($this->getAvailableRoles() as $key => $role) {
            if (in_array($role, $roles)) {
                $userRoles[] = $this->translator->trans($key);
            }
        }

        return join(', ', $userRoles);
    }
}
