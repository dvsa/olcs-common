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
trait LicenceGoodsVehiclesControllerTrait
{
    /**
     * Save the vehicle
     *
     * @param array $licenceVehicleId
     * @param string $mode
     */
    protected function postSaveVehicle($licenceVehicleId, $mode)
    {
        if ($mode == 'add' && !empty($licenceVehicleId)) {
            $this->requestDisc($licenceVehicleId);
        }
    }
}
