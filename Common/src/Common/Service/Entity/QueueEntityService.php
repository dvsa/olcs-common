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
    const STATUS_FAILED = 'que_sts_failed';

    // Message types
    const TYPE_COMPANIES_HOUSE_INITIAL = 'que_typ_ch_initial';
    const TYPE_COMPANIES_HOUSE_COMPARE = 'que_typ_ch_compare';
    const TYPE_CONT_CHECKLIST_REMINDER_GENERATE_LETTER = 'que_typ_cont_check_rem_gen_let';

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
        $result['attempts']++;

        $data = [
            'id' => $result['id'],
            'version' => $result['version'],
            'status' => self::STATUS_PROCESSING,
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
        $item['status'] = self::STATUS_QUEUED;

        $this->save($item);
    }

    public function complete($item)
    {
        $item['status'] = self::STATUS_COMPLETE;

        $this->save($item);
    }

    public function failed($item)
    {
        $item['status'] = self::STATUS_FAILED;

        $this->save($item);
    }
}
