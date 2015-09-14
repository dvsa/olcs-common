<?php

/**
 * Continuation Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Continuation Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ContinuationEntityService extends AbstractEntityService
{
    const TYPE_IRFO = 'irfo';
    const TYPE_OPERATOR = 'operator';

    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Continuation';
}
