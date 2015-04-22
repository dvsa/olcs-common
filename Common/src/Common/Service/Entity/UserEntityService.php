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
    const PERMISSION_SELFSERVE_LVA = 'selfserve-lva';
    const PERMISSION_SELFSERVE_TM_DASHBOARD = 'selfserve-tm-dashboard';

    protected $entity = 'User';

    protected $currentUserBundle = [
        'children' => [
            'team'
        ]
    ];

    protected $userDetailsBundle = [
        'children' => [
            'contactDetails' => [
                'children' => [
                    'person',
                    'contactType'
                ]
            ],
            'transportManager',
            'team',
            'userRoles' => [
                'children' => [
                    'role'
                ]
            ]
        ]
    ];

    protected $tmaBundle = [
        'children' => [
            'transportManager' => [
                'children' => [
                    'tmApplications' => [
                        'children' => [
                            'tmApplicationStatus',
                            'application' => [
                                'children' => [
                                    'licence'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];

    /**
     * Bundle for standard list
     *
     * @var array
     */
    protected $listBundle = [
        'children' => [
            'contactDetails' => [
                'children' => [
                    'person'
                ]
            ],
            'transportManager',
            'team',
            'userRoles' => [
                'children' => [
                    'role'
                ]
            ]
        ]
    ];

    /**
     * Get the current logged in user ID
     *
     * @todo when we have implemented auth, we need to amend this
     * @return int
     */
    public function getCurrentUserId()
    {
        return 1;
    }

    /**
     * Get the current user
     */
    public function getCurrentUser()
    {
        return $this->get($this->getCurrentUserId(), $this->currentUserBundle);
    }

    public function getUserDetails($id)
    {
        return $this->get($id, $this->userDetailsBundle);
    }

    /**
     * Get Transport Manager Applications for a User
     *
     * @param int $userId User ID
     * @return array Entity data tmApplications
     */
    public function getTransportManagerApplications($userId)
    {
        $query = [
            'id' => $userId,
        ];

        $results = $this->getAll($query, $this->tmaBundle);

        return (isset($results['transportManager']['tmApplications'])) ?
            $results['transportManager']['tmApplications'] :
            [];
    }
}
