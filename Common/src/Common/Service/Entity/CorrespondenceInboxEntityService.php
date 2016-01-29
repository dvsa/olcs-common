<?php

/**
 * Correspondence Inbox (email) Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Correspondence Inbox (email) Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class CorrespondenceInboxEntityService extends AbstractLvaEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'CorrespondenceInbox';

    /**
     * Bundle for displaying the correspondence in a table.
     *
     * @var array
     */
    protected $completeBundle = [
        'children' => [
            'document',
            'licence'
        ]
    ];

    /**
     * Get the full correspondence record by its primary identifier.
     *
     * @param $id The correspondence id
     *
     * @return array The correspondence record.
     */
    public function getById($id)
    {
        return parent::get($id, $this->completeBundle);
    }
}
