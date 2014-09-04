<?php

/**
 * Vehicle Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Application\VehicleSafety;

use Common\Controller\Traits\VehicleSection;

/**
 * Vehicle Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VehicleController extends VehicleSafetyController
{
    use VehicleSection;

    /**
     * Save the vehicle
     *
     * @todo might be able to combine these 2 methods now
     *
     * @param array $data
     * @param string $service
     */
    protected function actionSave($data, $service = null)
    {
        return $this->doActionSave($data, $this->getActionName());
    }
}
