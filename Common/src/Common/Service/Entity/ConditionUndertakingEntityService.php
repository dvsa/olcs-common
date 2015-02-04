<?php

/**
 * Condition Undertaking Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Condition Undertaking Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ConditionUndertakingEntityService extends AbstractEntityService
{
    const ATTACHED_TO_LICENCE = 'cat_lic';
    const ATTACHED_TO_OPERATING_CENTRE = 'cat_oc';

    protected $entity = 'ConditionUndertaking';
}
