<?php

/**
 * Queue Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

use Common\Exception\ResourceConflictException;

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

    // Message statuses
    const STATUS_QUEUED = 'que_sts_queued';
    const STATUS_PROCESSING = 'que_sts_processing';
    const STATUS_COMPLETE = 'que_sts_complete';

    protected $itemBundle = [
        'children' => [
            'status'
        ]
    ];

    public function getNextItem($type = null)
    {
        $now = $this->getServiceLocator()->get('Helper\Date')->getDate(\DateTime::W3C);

        $query = [
            'status' => self::STATUS_QUEUED,
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

        $data = [
            'id' => $result['id'],
            'version' => $result['version'],
            'status' => self::STATUS_PROCESSING
        ];

        try {
            $this->save($data);
        } catch (ResourceConflictException $ex) {
            return null;
        }

        return $result;
    }
}
