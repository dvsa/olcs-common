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

    /**
     * This hooks into the saveVehicle method of the abstract
     *
     * @param array $data
     * @return array
     */
    protected function alterVehicleDataForSave(array $data)
    {
        $data['licence-vehicle']['application'] = $this->getApplicationId();

        return $data;
    }

    /**
     * If we have the not-yet submitted status, then we should remove the reprint button
     *
     * @param \Common\Service\Table\TableBuilder
     */
    protected function alterTable($table)
    {
        $table->removeAction('reprint');
    }

    /**
     * Get current disc number
     *
     * @param array $licenceVehicle
     * @return string
     */
    protected function getCurrentDiscNo($licenceVehicle)
    {
        return 'Pending';
    }

    /**
     * Get the total vehicle authorisations
     *
     * @return int
     */
    protected function getTotalNumberOfAuthorisedVehicles()
    {
        return $this->getServiceLocator()->get('Entity\Application')
            ->getAuthorisedVehiclesTotal($this->getApplicationId());
    }
}
