<?php

/**
 * Application Generic Vehicle Section Trait
 *
 * Internal/External - Application - Vehicle/VehiclePsv Section
 *
 * @NOTE Includes shared logic between the APPLICATION/vehicle and APPLICATION/vehicle-psv sections
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\VehicleSafety;

/**
 * Application Generic Vehicle Section Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait ApplicationGenericVehicleSection
{
    /**
     * This is extended in the licence section
     *
     * @param array $licenceVehicle
     * @return boolean
     */
    protected function showVehicle($licenceVehicle)
    {
        return true;
    }
}
