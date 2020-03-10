<?php

/**
 * Transport Manager Application Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Transport Manager Application Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TransportManagerApplicationEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'TransportManagerApplication';

    protected $dataBundle = [
        'children' => [
            'application' => [
                'children' => [
                    'status',
                    'licence' => [
                        'children' => [
                            'organisation'
                        ]
                    ]
                ]
            ],
            'transportManager',
            'tmType',
            'operatingCentres',
            'tmApplicationStatus'
        ]
    ];

    protected $homeContactDetailsBundle = [
        'children' => [
            'application',
            'tmApplicationStatus',
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

    protected $tmDetailsBundle = [
        'children' => [
            'application',
            'transportManager' => [
                'children' => [
                    'homeCd' => [
                        'children' => [
                            'person',
                            'address'
                        ]
                    ],
                    'workCd' => [
                        'children' => [
                            'address'
                        ]
                    ]
                ]
            ],
            'tmType',
            'operatingCentres'
        ]
    ];

    protected $tmBundle = [
        'children' => [
            'transportManager'
        ]
    ];

    protected $reviewBundle = [
        'children' => [
            'otherLicences' => [
                'children' => [
                    'role'
                ]
            ],
            'tmType',
            'operatingCentres' => [
                'children' => [
                    'address'
                ]
            ],
            'application' => [
                'children' => [
                    'licence' => [
                        'children' => [
                            'organisation'
                        ]
                    ]
                ]
            ],
            'transportManager' => [
                'children' => [
                    'previousConvictions',
                    'otherLicences',
                    'employments' => [
                        'children' => [
                            'contactDetails' => [
                                'children' => [
                                    'address'
                                ]
                            ]
                        ]
                    ],
                    'documents' => [
                        'children' => [
                            'category',
                            'subCategory'
                        ],
                    ],
                    'workCd' => [
                        'children' => [
                            'address'
                        ]
                    ],
                    'homeCd' => [
                        'children' => [
                            'address',
                            'person' => [
                                'children' => [
                                    'title'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];

    protected $contactApplicationBundle = [
        'children' => [
            'tmApplicationStatus',
            'application' => [
                'children' => [
                    'status',
                    'licence' => [
                        'children' => [
                            'organisation'
                        ]
                    ]
                ]
            ],
            'transportManager' => [
                'children' => [
                    'homeCd' => [
                        'children' => [
                            'person',
                        ]
                    ],
                ]
            ],
        ]
    ];

    protected $markersBundle = [
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
     * Get Transport Manager application
     *
     * @param int $id
     * @return array
     */
    public function getTransportManagerApplication($id)
    {
        return $this->get($id, $this->dataBundle);
    }

    public function getByApplication($applicationId)
    {
        return $this->get(['application' => $applicationId]);
    }

    public function deleteForApplication($applicationId)
    {
        $query = ['application' => $applicationId];
        return $this->deleteList($query);
    }

    /**
     * Delete Transport Manager application(s)
     *
     * @param mixed $transportManagerApplicationId either one int ID or an array of int D's
     *
     * @return void
     */
    public function delete($transportManagerApplicationId)
    {
        if (!is_array($transportManagerApplicationId)) {
            $transportManagerApplicationId = array($transportManagerApplicationId);
        }

        $this->deleteListByIds(['id' => $transportManagerApplicationId]);
    }

    /**
     * Get TM applications for an application
     *
     * @param int $applicationId Application ID
     *
     * @return array ['Count' => n. 'Results' => array(...)]
     */
    public function getByApplicationWithHomeContactDetails($applicationId)
    {
        $query = ['application' => $applicationId];

        return $this->getAll($query, $this->homeContactDetailsBundle);
    }

    /**
     * Get Transport Manager applications linked to an application and a Transport Manager
     *
     * @param int $applicationId      Application ID
     * @param int $transportManagerId Transport Manager ID
     * @return array
     */
    public function getByApplicationTransportManager($applicationId, $transportManagerId)
    {
        $query = [
            'application' => $applicationId,
            'transportManager' => $transportManagerId,
        ];
        return $this->getAll($query);
    }

    public function getTransportManagerDetails($id)
    {
        return $this->get($id, $this->tmDetailsBundle);
    }

    public function getTransportManagerId($id)
    {
        $data = $this->get($id, $this->tmBundle);

        return $data['transportManager']['id'];
    }

    /**
     * Update the status of a Transport Manager Application
     *
     * @param int    $tmaId  Transport Manager Application ID
     * @param string $status New status, once of the constants RefData::TMA_STATUS_*
     * @return void
     */
    public function updateStatus($tmaId, $status)
    {
        $data = [
            'tmApplicationStatus' => $status
        ];

        $this->forceUpdate($tmaId, $data);
    }

    public function getReviewData($id)
    {
        return $this->get($id, $this->reviewBundle);
    }

    /**
     * Get contact details and application details for a Transport Manager Application
     *
     * @param int $tmaId Transport Manager Application Id
     * @return array
     */
    public function getContactApplicationDetails($tmaId)
    {
        return $this->get($tmaId, $this->contactApplicationBundle);
    }

    /**
     * Get Transport Managers for particular application
     *
     * @param int $applicationId
     * @return array
     */
    public function getTmForApplication($applicationId)
    {
        return $this->getAll(['application' => $applicationId], $this->markersBundle);
    }
}
