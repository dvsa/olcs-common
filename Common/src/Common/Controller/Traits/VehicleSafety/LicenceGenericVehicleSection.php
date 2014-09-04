<?php

/**
 * Licence Generic Vehicle Section Trait
 *
 * Internal/External - Licence - Vehicle/VehiclePsv Section
 *
 * @NOTE Includes shared logic between the LICENCE/vehicle and LICENCE/vehicle-psv sections
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\VehicleSafety;

/**
 * Licence Generic Vehicle Section Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait LicenceGenericVehicleSection
{
    /**
     * We only want to show active vehicles
     *
     * @param array $licenceVehicle
     * @return boolean
     */
    protected function showVehicle($licenceVehicle)
    {
        if (empty($licenceVehicle['specifiedDate'])) {
            return false;
        }

        return true;
    }
}
