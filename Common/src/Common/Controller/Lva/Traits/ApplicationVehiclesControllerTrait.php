<?php

/**
 * Application Vehicles Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

/**
 * Application Vehicles Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait ApplicationVehiclesControllerTrait
{
    /**
     * Whether to display the vehicle
     *
     * @param array $licenceVehicle
     * @return boolean
     */
    protected function showVehicle(array $licenceVehicle)
    {
        return empty($licenceVehicle['removalDate']);
    }
}
