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

    protected $mainBundle = [
        'children' => [
            'taskAllocationType'
        ]
    ];

    /**
     * Retrieve a category by its primary identifier
     */
    public function findById($id)
    {
        return $this->get($id, $this->mainBundle);
    }
}
