<?php

/**
 * Authorisation Controller
 *
 * External - Application - Authorisation Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Controller\Application\OperatingCentres;

use Common\Controller\Traits\OperatingCentre;

/**
 * Authorisation Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class AuthorisationController extends OperatingCentresController
{
    use OperatingCentre\GenericApplicationAuthorisationSection,
        OperatingCentre\ExternalApplicationAuthorisationSection;

    /**
     * Northern Ireland Traffic Area Code
     */
    const NORTHERN_IRELAND_TRAFFIC_AREA_CODE = 'N';
}
