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
class UserEntityService extends AbstractEntityService
{
    protected $entity = 'User';

    protected $currentUserBundle = array(
        'children' => array(
            'team'
        )
    );

    /**
     * Get the current user
     *
     * @todo when we have implemented auth, we need to amend this
     */
    public function getCurrentUser()
    {
        $id = 1;

        return $this->get($id, $this->currentUserBundle);
    }
}
