<?php

/**
 * Vehicle Controller
 *
 * External - Application - Vehicle section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Application\VehicleSafety;

use Common\Controller\Traits\VehicleSafety as VehicleSafetyTraits;

/**
 * Vehicle Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VehicleController extends VehicleSafetyController
{
    use VehicleSafetyTraits\VehicleSection,
        VehicleSafetyTraits\ExternalGenericVehicleSection,
        VehicleSafetyTraits\ApplicationGenericVehicleSection,
        VehicleSafetyTraits\GenericApplicationVehicleSection;
}
