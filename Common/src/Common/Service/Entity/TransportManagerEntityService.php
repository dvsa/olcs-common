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
    
    protected $tmDetailsBundle = [
        'properties' => [
            'version'
        ],
        'children' => [
            'contactDetails' => [
                'properties' => [
                    'id',
                    'version',
                    'emailAddress'
                ],
                'children' => [
                    'person' => [
                        'properties' => [
                            'id',
                            'version',
                            'forename',
                            'familyName',
                            'title',
                            'birthDate',
                            'birthPlace'
                        ]
                    ],
                    'address' => [
                        'properties' => [
                            'id',
                            'version',
                            'addressLine1',
                            'addressLine2',
                            'addressLine3',
                            'addressLine4',
                            'town',
                            'postcode'
                        ]
                    ],
                    'contactType' => [
                        'properties' => [
                            'id'
                        ]
                    ]
                ]
            ],
            'tmType' => [
                'properties' => [
                    'id'
                ]
            ],
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
}
