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

        if (isset($filters['licenceNo']) && !empty($filters['licenceNo'])) {
            $bundle['children']['licence']['criteria']['licNo'] = $filters['licenceNo'];
        }

        if (isset($filters['licenceStatus']) && is_array($filters['licenceStatus'])) {
            $bundle['children']['licence']['criteria']['status'] = $filters['licenceStatus'];
        }

        if (isset($filters['method']) && in_array($filters['method'], ['post', 'email'])) {
            if ($filters['method'] === 'post') {
                $bundle['children']['licence']['children']['organisation']['criteria']['allowEmail'] = 0;
            } else {
                $bundle['children']['licence']['children']['organisation']['criteria']['allowEmail'] = 1;
            }
        }

        if (isset($filters['status']) && !empty($filters['status'])) {
            $query['status'] = $filters['status'];
        }

        return $this->getAll($query, $bundle);
    }
}
