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
                'children' => [
                    'status',
                    'organisation',
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

    public function getListData($continuationId)
    {
        $query = [
            'continuation' => $continuationId
        ];

        return $this->getAll($query, $this->listBundle);
    }
}
