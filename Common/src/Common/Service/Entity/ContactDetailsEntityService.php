<?php

/**
 * Contact Details Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Contact Details Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ContactDetailsEntityService extends AbstractEntityService
{
    const CONTACT_TYPE_ESTABLISHMENT = 'ct_est';
    const CONTACT_TYPE_CORRESPONDENCE = 'ct_corr';
    const CONTACT_TYPE_REGISTERED = 'ct_reg';
    const CONTACT_TYPE_WORKSHOP = 'ct_work';

    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'contactDetails';
}
