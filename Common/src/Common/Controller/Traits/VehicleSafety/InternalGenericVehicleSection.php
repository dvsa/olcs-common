<?php

/**
 * Internal Generic Vehicle Section
 *
 * Internal - Application/Licence - Vehicle/VehiclePsv Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\VehicleSafety;

/**
 * Internal Generic Vehicle Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait InternalGenericVehicleSection
{
    protected $sectionLocation = 'Internal';

    /**
     * Holds the table data bundle
     *
     * @var array
     */
    protected $historyTableDataBundle = array(
        'properties' => array(
            'id',
            'vrm',
            'licenceNo',
            'specifiedDate',
            'removalDate',
            'discNo'
        )
    );


    /**
     * Holds the bundle to retrieve VRM
     *
     * @var array
     */
    protected $vehicleBundle = array(
        'properties' => array(

        ),
        'children' => array(
            'vehicle' => array(
                'properties' => array(
                    'vrm'
                )
            )
        )
    );

    /**
     * Alter the action form
     *
     * @param Form $form
     * @return Form
     */
    protected function alterActionForm($form)
    {
        return $this->doAlterActionForm($form);
    }

    /**
     * Get the form table data
     *
     * @param int $id
     * @param string $table
     * @return array
     */
    protected function getActionTableData($id)
    {
        $vehicleId=$this->getActionId();

        if ( is_null($vehicleId) ) {
            return array();
        }

        $vrmData = $this->makeRestCall(
            'LicenceVehicle',
            'GET',
            array('id' => $vehicleId),
            $this->vehicleBundle
        );

        $data = $this->makeRestCall(
            'VehicleHistoryView',
            'GET',
            array(
                'vrm' => $vrmData['vehicle']['vrm'],
                'sort' => 'specifiedDate',
                'order' => 'DESC'
            ),
            $this->historyTableDataBundle
        );

        return $data;
    }


    /**
     * Shared logic between internal vehicle sections
     *
     * @param array $data
     * @param string $action
     * @return mixed
     */
    protected function internalActionSave($data, $action)
    {
        if ($action == 'add') {
            $data['licence-vehicle']['specifiedDate'] = date('Y-m-d');
        }

        return $this->doActionSave($data, $action);
    }
}
