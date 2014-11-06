<?php

/**
 * Fee Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Fee Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FeeEntityService extends AbstractLvaEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Fee';

    const STATUS_OUTSTANDING = 'lfs_ot';
    const STATUS_PAID = 'Paid';
    const STATUS_WAIVE_RECOMMENDED = 'lfs_wr';
    const STATUS_WAIVED = 'lfs_w';
    const STATUS_CANCELLED = 'lfs_cn';
}
