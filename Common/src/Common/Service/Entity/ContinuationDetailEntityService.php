<?php

/**
 * Continuation Detail Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Continuation Detail Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ContinuationDetailEntityService extends AbstractEntityService
{
    const STATUS_PREPARED = 'con_det_sts_prepared';
    const STATUS_PRINTING = 'con_det_sts_printing';
    const STATUS_PRINTED = 'con_det_sts_printed';

    const STATUS_UNACCEPTABLE = 'con_det_sts_unacceptable';
    const STATUS_ACCEPTABLE = 'con_det_sts_acceptable';
    const STATUS_COMPLETE = 'con_det_sts_complete';
    const STATUS_ERROR = 'con_det_sts_error';

    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'ContinuationDetail';
}
