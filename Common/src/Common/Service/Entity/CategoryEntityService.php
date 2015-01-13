<?php

/**
 * Category Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Category Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class CategoryEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Category';

    /**
     * Make sure we return the task allocation type ref data
     * key
     *
     * @var array
     */
    protected $mainBundle = [
        'children' => [
            'taskAllocationType'
        ]
    ];

    /**
     * Retrieve a category by its primary identifier
     *
     * @param int $id
     * @return mixed
     */
    public function findById($id)
    {
        return $this->get($id, $this->mainBundle);
    }
}
