<?php

/**
 * Queue Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

use Common\Exception\ResourceConflictException;
use Common\RefData;

/**
 * Queue Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QueueEntityService extends AbstractEntityService
{
    /**
     * Entity reference.
     *
     * @var string
     */
    protected $entity = 'Queue';

    protected $itemBundle = [
        'children' => [
            'status',
            'type'
        ]
    ];

    public function getNextItem($type = null)
    {
        $now = $this->getServiceLocator()->get('Helper\Date')->getDate(\DateTime::W3C);

        $query = [
            'status' => RefData::QUEUE_STATUS_QUEUED,
            'limit' => 1,
            'sort' => 'createdOn',
            'order' => 'ASC',
            'processAfterDate' => [
                'NULL',
                '<=' . $now
            ]
        ];

        if ($type !== null) {
            $query['type'] = $type;
        }

        $results = $this->get($query, $this->itemBundle);

        if (empty($results['Results'])) {
            return null;
        }

        $result = $results['Results'][0];
        $result['attempts']++;

        $data = [
            'id' => $result['id'],
            'version' => $result['version'],
            'status' => RefData::QUEUE_STATUS_PROCESSING,
            'attempts' => $result['attempts']
        ];

        try {
            $this->save($data);
            $result['version']++;
        } catch (ResourceConflictException $ex) {
            return null;
        }

        return $result;
    }

    public function retry($item)
    {
        $item['status'] = RefData::QUEUE_STATUS_QUEUED;

        $this->save($item);
    }

    public function complete($item)
    {
        $item['status'] = RefData::QUEUE_STATUS_COMPLETE;

        $this->save($item);
    }

    public function failed($item)
    {
        $item['status'] = RefData::QUEUE_STATUS_FAILED;

        $this->save($item);
    }
}
