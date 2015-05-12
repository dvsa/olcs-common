<?php

/**
 * Continuation Detail Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Continuation Detail Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ContinuationDetailEntityService extends AbstractEntityService
{
    const STATUS_PREPARED = 'con_det_sts_prepared';
    const STATUS_PRINTING = 'con_det_sts_printing';
    const STATUS_PRINTED = 'con_det_sts_printed';

    const STATUS_UNACCEPTABLE = 'con_det_sts_unacceptable';
    const STATUS_ACCEPTABLE = 'con_det_sts_acceptable';
    const STATUS_COMPLETE = 'con_det_sts_complete';
    const STATUS_ERROR = 'con_det_sts_error';

    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'ContinuationDetail';

    protected $listBundle = [
        'children' => [
            'status',
            'licence' => [
                'required' => true,
                'sort' => 'licNo',
                'order' => 'ASC',
                'children' => [
                    'status',
                    'organisation' => [
                        'required' => true
                    ],
                    'licenceType',
                    'goodsOrPsv',
                ]
            ]
        ]
    ];

    protected $detailsBundle = [
        'children' => [
            'status',
            'licence' => [
                'children' => [
                    'licenceType',
                    'goodsOrPsv'
                ]
            ]
        ]
    ];

    public function createRecords($records)
    {
        $this->multiCreate($records);
    }

    /**
     * Filter detail list data
     */
    public function getListData($continuationId, array $filters = [])
    {
        $query = [
            'continuation' => $continuationId
        ];

        $bundle = $this->listBundle;

        $licenceCriteria = $this->getLicenceCriteria($filters);

        if ($licenceCriteria !== null) {
            $bundle['children']['licence']['criteria'] = $licenceCriteria;
        }

        $organisationCriteria = $this->getOrgCriteria($filters);

        if ($organisationCriteria !== null) {
            $bundle['children']['licence']['children']['organisation']['criteria'] = $organisationCriteria;
        }

        if (isset($filters['status']) && !empty($filters['status'])) {
            $query['status'] = $filters['status'];
        }

        return $this->getAll($query, $bundle);
    }

    protected function getOrgCriteria($filters)
    {
        if (isset($filters['method']) && in_array($filters['method'], ['post', 'email'])) {
            if ($filters['method'] === 'post') {
                return ['allowEmail' => 0];
            }
            return ['allowEmail' => 1];
        }

        return null;
    }

    protected function getLicenceCriteria($filters)
    {
        $criteria = [];

        if (isset($filters['licenceNo']) && !empty($filters['licenceNo'])) {
            $criteria['licNo'] = $filters['licenceNo'];
        }

        if (isset($filters['licenceStatus']) && is_array($filters['licenceStatus'])) {
            $criteria['status'] = $filters['licenceStatus'];
        }

        if (empty($criteria)) {
            return null;
        }

        return $criteria;
    }

    /**
     * Get continuation details for a licence. This will only return continuation details if it matches the criteria
     *
     * @param int $licenceId
     *
     * @return array
     */
    public function getContinuationMarker($licenceId)
    {
        /* @var $dateTime \DateTime */
        $dateTime = $this->getServiceLocator()->get('Helper\Date')->getDateObject();
        $year = $dateTime->format('Y');
        $month = $dateTime->format('n');

        $dateTime->modify('+4 years');
        $yearFuture = $dateTime->format('Y');
        $monthFuture = $month;

        $query = [
            'licence' => $licenceId,
            [
                [
                    'status' => [self::STATUS_PRINTED, self::STATUS_ACCEPTABLE, self::STATUS_UNACCEPTABLE],
                ],
                [
                    'status' => self::STATUS_COMPLETE,
                    'received' => 0
                ]
            ]
        ];

        $bundle = [
            'children' => [
                'status',
                'licence' => [
                    'children' => ['status'],
                    'criteria' => [
                        'status' => [
                            LicenceEntityService::LICENCE_STATUS_VALID,
                            LicenceEntityService::LICENCE_STATUS_CURTAILED,
                            LicenceEntityService::LICENCE_STATUS_SUSPENDED,
                        ]
                    ],
                    'required' => true,
                ],
                'continuation' => [
                    'criteria' => [
                        [
                            [
                                'year' => $year,
                                'month' => '>= ' . $month
                            ],
                            [
                                'year' => [
                                    [
                                        '> ' . $year,
                                        '< ' . $yearFuture
                                    ]
                                ]
                            ],
                            [
                                'year' => $yearFuture,
                                'month' => '< ' . $monthFuture
                            ]
                        ]
                    ],
                    'required' => true,
                ]
            ]
        ];

        return $this->getAll($query, $bundle);
    }

    public function checklistFailed($id)
    {
        $data = ['status' => self::STATUS_ERROR];

        $this->forceUpdate($id, $data);
    }

    public function getDetailsForProcessing($id)
    {
        return $this->get($id, $this->detailsBundle);
    }

    /**
     * @NOTE this method has a custom endpoint, as it must be wrapped within a transaction
     *
     * @param array $ids
     */
    public function generateChecklists($ids)
    {
        return $this->getServiceLocator()->get('Helper\Rest')
            ->makeRestCall('ContinuationDetail/Checklists', 'POST', ['ids' => $ids]);
    }

    /**
     * @NOTE this method has a custom endpoint, as it must be wrapped within a transaction
     */
    public function processContinuationDetail($id, $docId, $template)
    {
        $data = ['id' => $id, 'docId' => $docId, 'template' => $template];

        return $this->getServiceLocator()->get('Helper\Rest')
            ->makeRestCall('ContinuationDetail/Checklists', 'PUT', $data);
    }

    /**
     * Get ongoing continuation detail for a licence
     *
     * @param int $licenceId Licence ID
     *
     * @return array Entity data ['Count' => x, 'Results' => [y]]
     */
    public function getOngoingForLicence($licenceId)
    {
        $query = [
            'licence' => $licenceId,
            'status' => self::STATUS_ACCEPTABLE,
        ];
        $bundle = [
            'children' => [
                'licence' => [
                    'children' => [
                        'status',
                    ]
                ],
            ]
        ];

        $results = $this->getAll($query, $bundle);

        // there should only every be one ongoing continuation
        if ($results['Count'] === 0) {
            return false;
        }
        return $results['Results'][0];
    }
}
