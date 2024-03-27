<?php

declare(strict_types=1);

namespace Common\Rbac\Service;

use Common\RefData;
use LmcRbacMvc\Service\AuthorizationService;

class Permission
{
    private AuthorizationService $authService;

    public function __construct(AuthorizationService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Returns true if the user is internal read only, or internal limited read only.
     * Returns false for all other users
     */
    public function isInternalReadOnly(): bool
    {
        return $this->authService->isGranted(RefData::PERMISSION_INTERNAL_USER)
            && !$this->authService->isGranted(RefData::PERMISSION_INTERNAL_EDIT);
    }

    public function isGranted(string $permission, $context = null): bool
    {
        return $this->authService->isGranted($permission, $context);
    }

    public function isSelf(string $userId): bool
    {
        $userData = $this->authService->getIdentity()->getUserData();

        $currentUserId = $userData['id'] ?? null;

        if ($currentUserId === null) {
            return false;
        }

        return (string) $currentUserId === $userId;
    }
}
