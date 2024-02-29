<?php

namespace Common\Rbac\Traits;

use Common\RefData;
use LmcRbacMvc\Service\AuthorizationService;

trait Permission
{
    protected AuthorizationService $authService;

    /**
     * Returns true if the user is internal read only, or internal limited read only.
     * Returns false for all other users
     */
    public function isInternalReadOnly(): bool
    {
        return $this->authService->isGranted(RefData::PERMISSION_INTERNAL_USER)
            && !$this->authService->isGranted(RefData::PERMISSION_INTERNAL_EDIT);
    }
}
