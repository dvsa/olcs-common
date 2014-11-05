<?php

/**
 * Application Vehicles Controller Trait
 *
 * @NOTE This should be used by vehicles and PSV vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

/**
 * Application Vehicles Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait ApplicationGenericVehiclesControllerTrait
{
    abstract function getApplicationId();

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

    /**
     * For applications we need to set the application id
     *
     * @param array $data
     * @return array
     */
    protected function alterDataForLva($data)
    {
        $data['licence-vehicle']['application'] = $this->getApplicationId();

        return $data;
    }
}
