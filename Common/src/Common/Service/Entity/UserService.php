<?php

/**
 * User Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * User Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UserService extends AbstractEntityService
{
    /**
     * Get the current user
     *
     * @todo when we have implemented auth, we need to ammend this
     */
    public function getCurrentUser()
    {
        return array('id' => 1);
    }
}
