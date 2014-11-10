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
    protected $currentUserBundle = array(
        'properties' => array(
            'id'
        ),
        'children' => array(
            'team' => array(
                'properties' => array('id')
            )
        )
    );

    protected $entity = 'User';

    /**
     * Get the current user
     *
     * @todo when we have implemented auth, we need to ammend this
     */
    public function getCurrentUser()
    {
        $id = 1;

        return $this->get($id, $this->currentUserBundle);
    }
}
