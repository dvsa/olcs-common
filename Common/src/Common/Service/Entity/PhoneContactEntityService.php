<?php

/**
 * Phone Contact Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Phone Contact Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PhoneContactEntityService extends AbstractEntityService
{
    const TYPE_BUSINESS = 'phone_t_tel';
    const TYPE_HOME = 'phone_t_home';
    const TYPE_MOBILE = 'phone_t_mobile';
    const TYPE_FAX = 'phone_t_fax';

    protected $entity = 'PhoneContact';
}
