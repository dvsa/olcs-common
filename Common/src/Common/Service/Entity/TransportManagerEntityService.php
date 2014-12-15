<?php

/**
 * Transport Manager Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Transport Manager Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TransportManagerEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'TransportManager';

    const TRANSPORT_MANAGER_STATUS_ACTIVE = 'tm_st_A';

    const TRANSPORT_MANAGER_STATUS_DISABLED = 'tm_st_D';

    protected $tmDetailsBundle = [
        'children' => [
            'contactDetails' => [
                'children' => [
                    'person',
                    'address',
                    'contactType'
                ]
            ],
            'tmType',
            'tmStatus'
        ]
    ];

    /**
     * Get transport manager details
     *
     * @param int $id
     */
    public function getTmDetails($id)
    {
        return $this->get($id, $this->tmDetailsBundle);
    }

    public function findByIdentifier($identifier)
    {
        return $this->get($identifier);
    }
}
