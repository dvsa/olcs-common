<?php

/**
 * Task Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Task Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TaskEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Task';

    const STATUS_OPEN = 'tst_open';
    const STATUS_CLOSED = 'tst_closed';
    const STATUS_ALL = 'tst_all';
}
