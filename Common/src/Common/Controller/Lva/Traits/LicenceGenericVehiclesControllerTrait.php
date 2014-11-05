<?php

/**
 * Licence Vehicles Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

/**
 * Licence Vehicles Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait LicenceGenericVehiclesControllerTrait
{
    /**
     * We only want to show active vehicles
     *
     * @param array $licenceVehicle
     * @return boolean
     */
    protected function showVehicle(array $licenceVehicle)
    {
        return (!empty($licenceVehicle['specifiedDate']) || empty($licenceVehicle['removalDate']));
    }
}
