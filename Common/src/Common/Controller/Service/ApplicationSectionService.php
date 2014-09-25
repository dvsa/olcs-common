<?php

/**
 * Application Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Service;

/**
 * Application Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationSectionService extends AbstractSectionService
{
    /**
     * Application statuses
     */
    const APPLICATION_STATUS_NOT_YET_SUBMITTED = 'apsts_not_submitted';
    const APPLICATION_STATUS_CURTAILED = 'apsts_curtailed';
    const APPLICATION_STATUS_GRANTED = 'apsts_granted';
    const APPLICATION_STATUS_NOT_TAKEN_UP = 'apsts_ntu';
    const APPLICATION_STATUS_REFUSED = 'apsts_refused';
    const APPLICATION_STATUS_VALID = 'apsts_valid';
    const APPLICATION_STATUS_WITHDRAWN = 'apsts_withdrawn';
    const APPLICATION_STATUS_UNDER_CONSIDERATION = 'apsts_consideration';
}
