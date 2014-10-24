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
     * If we have the not-yet submitted status, then we should remove the reprint button
     *
     * @param \Common\Service\Table\TableBuilder
     */
    protected function alterTable($table)
    {
        $applicationStatus = $this->getApplicationStatus();

        if ($applicationStatus == self::APPLICATION_STATUS_NOT_YET_SUBMITTED) {
            $table->removeAction('reprint');
        }

        // it's not ideal checking this property here, but these traits are all being
        // reworked as part of OLCS-4522 anyway
        if ($this->sectionLocation === 'External') {
            $table->removeAction('print-vehicles');
        }

        return $table;
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
