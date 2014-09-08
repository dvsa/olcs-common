<?php

/**
 * Generic Application Vehicle Section Trait
 *
 * Internal/External - Application - Vehicle Section
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
    protected $totalNumberOfVehiclesBundle = array(
        'properties' => array(),
        'children' => array(
            'licence' => array(
                'properties' => array(),
                'children' => array(
                    'licenceVehicles' => array(
                        'properties' => array('id')
                    )
                )
            )
        )
    );

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

    /**
     * Get total number of vehicles
     *
     * @return int
     */
    protected function getTotalNumberOfVehicles()
    {
        $data = $this->makeRestCall(
            'Application',
            'GET',
            array('id' => $this->getIdentifier()),
            $this->totalNumberOfVehiclesBundle
        );

        return count($data['licence']['licenceVehicles']);
    }
}
