<?php

/**
 * TaskAllocationRule Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * TaskAllocationRule Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TaskAllocationRuleEntityService extends AbstractEntityService
{
    /**
     * Ref data constants
     */
    const TYPE_SIMPLE  = 'task_at_simple';
    const TYPE_MEDIUM  = 'task_at_medium';
    const TYPE_COMPLEX = 'task_at_complex';

    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'TaskAllocationRule';

    /**
     * The main data bundle; we always want our team and users populating
     *
     * @var array
     */
    protected $mainBundle = [
        'children' => [
            'team', 'user'
        ]
    ];

    /**
     * Generic method to find records by an input query
     *
     * @param array $query
     * @return mixed
     */
    public function findByQuery(array $query = [])
    {
        return $this->get($query, $this->mainBundle);
    }
}
