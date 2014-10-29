<?php

/**
 * Bookmark Search Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Bookmark Search Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BookmarkSearchEntityService extends AbstractLvaEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'BookmarkSearch';

    public function searchQuery($query)
    {
        return $this->get([], $query);
    }
}
