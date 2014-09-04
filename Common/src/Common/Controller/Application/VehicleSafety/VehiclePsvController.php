<?php

/**
 * Vehicle PSV Controller
 *
 * External - Application - Vehicle PSV section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Controller\Application\VehicleSafety;

use Common\Controller\Traits\VehicleSafety as VehicleSafetyTraits;

/**
 * Vehicle PSV Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VehiclePsvController extends VehicleSafetyController
{
    use VehicleSafetyTraits\VehiclePsvSection,
        VehicleSafetyTraits\ExternalGenericVehicleSection,
        VehicleSafetyTraits\ApplicationGenericVehicleSection,
        VehicleSafetyTraits\GenericApplicationVehiclePsvSection;
}
