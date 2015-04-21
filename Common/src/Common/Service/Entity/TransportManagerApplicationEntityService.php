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
    const STATUS_INCOMPLETE = 'tmap_st_incomplete';
    const STATUS_AWAITING_SIGNATURE = 'tmap_st_awaiting_signature';
    const STATUS_TM_SIGNED = 'tmap_st_tm_signed';
    const STATUS_OPERATOR_SIGNED = 'tmap_st_operator_signed';
    const STATUS_POSTAL_APPLICATION = 'tmap_st_postal_application';
    const STATUS_RECEIVED = 'tmap_st_received';

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

    protected $grantDataBundle = [
        'children' => [
            'application',
            'transportManager',
            'tmType',
            'operatingCentres',
            'otherLicences'
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

    protected $contactApplicationBundle = [
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

    public function getGrantDataForApplication($applicationId)
    {
        return $this->getAll(['application' => $applicationId], $this->grantDataBundle)['Results'];
    }

    /**
     * Get transport manager applications
     *
     * @param int $id
     * @param array $status
     * @return array
     */
    public function getTransportManagerApplications($id, $status = [])
    {
        $results = $this->getAll(['transportManager' => $id, 'action' => '!=D'], $this->dataBundle);

        $finalResults = [];

        foreach ($results['Results'] as &$result) {

            if (in_array($result['application']['status']['id'], $status)) {
                $result['ocCount'] = count($result['operatingCentres']);
                $finalResults[] = $result;
            }
        }

        return $finalResults;
    }

    /**
     * Get transport manager application
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
     * Delete transport manager application(s)
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
     * Get transport manager applications linked to an application and a transport manager
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
     * @param string $status New status, once of the constants self::STATUS_*
     * @return void
     */
    public function updateStatus($tmaId, $status)
    {
        $data = [
            'tmApplicationStatus' => $status
        ];

        $this->forceUpdate($tmaId, $data);
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
}
