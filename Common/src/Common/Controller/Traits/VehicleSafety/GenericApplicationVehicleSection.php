<?php

/**
 * Generic Application Vehicle Section Trait
 *
 * Internal/External - Application - Vehicle Section
 *
 * @NOTE Includes shared logic between the APPLICATION/vehicle, both internally and externally
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\VehicleSafety;

/**
 * Generic Application Vehicle Section Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait GenericApplicationVehicleSection
{
    /**
     * Save the vehicle
     *
     * @param array $data
     * @param string $service
     */
    protected function actionSave($data, $service = null)
    {
        return $this->doActionSave($data, $this->getActionName());
    }
}
