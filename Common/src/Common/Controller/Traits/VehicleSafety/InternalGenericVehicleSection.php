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
     * Form tables name
     *
     * @var string
     */
    protected $actionTables = array(
        'table' => 'vehicle_history'
    );

    /**
     * Holds the table data bundle
     *
     * @var array
     */
    protected $actionTableDataBundle = array(
        'properties' => array(
            'id',
            'vrm',
            'licenceNo',
            'specifiedDate',
            'deletedDate',
            'discNo'
        ),

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
        $vrmData = $this->makeRestCall(
            'VehicleHistoryView',
            'GET',
            array('vehicle_id' => $vehicleId),
            $this->actionTableDataBundle
        );

        $data = $this->makeRestCall(
            'VehicleHistoryView',
            'GET',
            array('vrm' => $vrmData['vrm']),
            $this->actionTableDataBundle
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
