<?php

/**
 * Application Vehicles Psv Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Interfaces\VehiclesAdapterInterface;

/**
 * Application Vehicles Psv Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationVehiclesPsvAdapter extends AbstractVehiclesPsvAdapter
{
    protected $vehiclesData; // cache

    protected $entityData; // cache

    /**
     * Get vehicles data for the given resource
     *
     * @param int $id
     * @return array
     */
    public function getVehiclesData($id)
    {
        if (is_null($this->vehiclesData)) {
            $this->vehiclesData = $this->getServiceLocator()->get('Entity\Licence')
                ->getVehiclesPsvDataForApplication($id);
        }

        return $this->vehiclesData;
    }

    /**
     * Get entity data for the given resource
     *
     * @param int $id
     * @return array
     */
    protected function getEntityData($id)
    {
        if (is_null($this->entityData)) {
            $this->entityData = $this->getServiceLocator()->get('Entity\Application')
                ->getDataForVehiclesPsv($id);
        }

        return $this->entityData;
    }

    /**
     * Add a flash warning message if the individual authority for a type of
     * vehicle is exceeded. This is an edge case and should only happen when
     * vehicles are added and then Operating Centre authority is decreased.
     *
     * @param int $applicationId
     * @param array $types vehicle types
     * @param boolean $redirecting whether we are rendering or redirecting
     * dictates which flash message method we use
     */
    public function warnIfAuthorityExceeded($applicationId, $psvTypes, $redirecting)
    {
        $data = $this->getEntityData($applicationId);

        // we only show warning if user has answered that the are submitting vehicle details
        if ($data['hasEnteredReg'] !== 'Y') {
            return;
        }

        $method = $redirecting ? 'addWarningMessage' : 'addCurrentWarningMessage';

        $vehicleEntityService = $this->getServiceLocator()->get('Entity\Vehicle');

        foreach ($psvTypes as $psvType) {
            $type = $vehicleEntityService->getTypeFromPsvType($psvType);
            $vehicles  = (int)$this->getVehicleCountByPsvType($applicationId, $psvType);
            $authority = (int)$this->getVehicleAuthByType($applicationId, $type);
            if ($vehicles>$authority) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->$method(
                    'more-vehicles-than-'.$type.'-authorisation'
                );
            }
        }
    }

    /**
     * Get vehicle authorisation for a particular type
     * @param int $applicationId
     * @param string $type
     * @return int|null
     */
    protected function getVehicleAuthByType($applicationId, $type)
    {
        $data = $this->getEntityData($applicationId);
        $authField = 'totAuth' . ucwords($type) . 'Vehicles';
        return (int) $data[$authField];
    }

    /**
     * Disable removed and specified dates if needed
     *
     * @param Zend\Form\Form $form
     * @param Common\Service\Helper\FormHelper
     */
    public function maybeDisableRemovedAndSpecifiedDates($form, $formHelper)
    {
        $dataFieldset = $form->get('licence-vehicle');
        $formHelper->disableDateElement($dataFieldset->get('specifiedDate'));
        $formHelper->disableDateElement($dataFieldset->get('removalDate'));
    }

    /**
     * Format removed and specified dates if needed
     *
     * @param array $licenceVehicle
     * @return array
     */
    public function maybeFormatRemovedAndSpecifiedDates($licenceVehicle)
    {
        return $licenceVehicle;
    }

    /**
     * Unset specified date if needed
     *
     * @param array $data
     * @return array
     */
    public function maybeUnsetSpecifiedDate($data)
    {
        unset($data['licence-vehicle']['specifiedDate']);
        return $data;
    }

    /**
     * Don't create an empty option in edit mode for specified date
     *
     * @param Zend\Form\Form $form
     * @param string $mode
     * @return Zend\Form\Form
     */
    public function maybeRemoveSpecifiedDateEmptyOption($form, $mode)
    {
        return $form;
    }
}
