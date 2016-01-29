<?php

/**
 * Transport Manager Licence Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Transport Manager Licence Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TransportManagerLicenceEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'TransportManagerLicence';

    protected $dataBundle = [
        'children' => [
            'licence' => [
                'children' => [
                    'organisation',
                    'status'
                ]
            ],
            'transportManager' => [
                'children' => [
                    'tmType'
                ]
            ],
            'tmType',
            'operatingCentres'
        ]
    ];

    protected $homeContactDetailsBundle = [
        'children' => [
            'transportManager' => [
                'children' => [
                    'homeCd' => [
                        'children' => [
                            'person'
                        ]
                    ],
                ]
            ]
        ],
    ];

    protected $markersBundle = [
        'children' => [
            'licence' => [
                'children' => [
                    'status',
                    'licenceType',
                    'organisation'
                ]
            ],
            'transportManager' => [
                'children' => [
                    'qualifications' => [
                        'children' => [
                            'qualificationType'
                        ]
                    ],
                    'tmType',
                    'homeCd' => [
                        'children' => [
                            'person'
                        ]
                    ],
                    'tmApplications' => [
                        'children' => [
                            'application' => [
                                'children' => [
                                    'status',
                                    'licenceType',
                                    'licence' => [
                                        'children' => [
                                            'organisation'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'tmLicences' => [
                        'children' => [
                            'licence' => [
                                'children' => [
                                    'status',
                                    'licenceType',
                                    'organisation'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];

    /**
     * Get Transport Manager licence
     *
     * @param int $id
     * @return array
     */
    public function getTransportManagerLicence($id)
    {
        return $this->get($id, $this->dataBundle);
    }

    public function getByTransportManagerAndLicence($transportManagerId, $licenceId)
    {
        $query = [
            'transportManager' => $transportManagerId,
            'licence' => $licenceId
        ];

        return $this->get($query)['Results'];
    }

    public function deleteForLicence($licenceId)
    {
        $query = ['licence' => $licenceId];
        return $this->deleteList($query);
    }

    /**
     * Get Transport Managers and their contact details for a licence
     *
     * @param int $licenceId Licence ID
     *
     * @return array
     */
    public function getByLicenceWithHomeContactDetails($licenceId)
    {
        $query = [
            'licence' => $licenceId,
            'sort'  => 'id',
            'order' => 'DESC',
        ];

        return $this->getAll($query, $this->homeContactDetailsBundle);
    }

    /**
     * Get Transport Managers for particular licence
     *
     * @param int $licenceId
     * @return array
     */
    public function getTmForLicence($licenceId)
    {
        return $this->getAll(['licence' => $licenceId], $this->markersBundle);
    }
}
