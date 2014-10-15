<?php

/**
 * Address Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Address Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AddressEntityService extends AbstractEntityService
{
    const CONTACT_TYPE_REGISTERED_ADDRESS = 'ct_reg';

    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'address';
}
