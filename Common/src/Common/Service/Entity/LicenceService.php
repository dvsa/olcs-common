<?php

/**
 * Licence Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Licence Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceService extends AbstractEntityService
{
    const LICENCE_STATUS_NEW = 'lsts_new';

    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Licence';
}
