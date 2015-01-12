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
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'TaskAllocationRule';

    protected $mainBundle = [
        'children' => [
            'team', 'user'
        ]
    ];

    public function findByQuery(array $query = [])
    {
        return $this->get($query, $this->mainBundle);
    }
}
