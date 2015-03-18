<?php

/**
 * Licence Vehicles Psv Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Interfaces\VehiclesAdapterInterface;

/**
 * Licence Vehicles Psv Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceVehiclesPsvAdapter extends AbstractAdapter implements VehiclesAdapterInterface
{
    /**
     * Get vehicles data for the given resource
     *
     * @param int $id
     * @return array
     */
    public function getVehiclesData($id)
    {
        return $this->getServiceLocator()->get('Entity\Licence')->getVehiclesPsvData($id);
    }

    /**
     * Format removed and specified dates if needed
     *
     * @param array $licenceVehicle
     * @return array
     */
    public function maybeFormatRemovedAndSpecifiedDates($licenceVehicle)
    {
        if (isset($licenceVehicle['specifiedDate'])) {
            if (checkdate(
                (int)$licenceVehicle['specifiedDate']['month'],
                (int)$licenceVehicle['specifiedDate']['day'],
                (int)$licenceVehicle['specifiedDate']['year']
            )) {
                $licenceVehicle['specifiedDate'] = sprintf(
                    '%s-%s-%s',
                    $licenceVehicle['specifiedDate']['year'],
                    $licenceVehicle['specifiedDate']['month'],
                    $licenceVehicle['specifiedDate']['day']
                );
            } else {
                unset($licenceVehicle['specifiedDate']);
            }
        }
        if (isset($licenceVehicle['removalDate'])) {
            if (checkdate(
                (int)$licenceVehicle['removalDate']['month'],
                (int)$licenceVehicle['removalDate']['day'],
                (int)$licenceVehicle['removalDate']['year']
            )) {
                $licenceVehicle['removalDate'] = sprintf(
                    '%s-%s-%s',
                    $licenceVehicle['removalDate']['year'],
                    $licenceVehicle['removalDate']['month'],
                    $licenceVehicle['removalDate']['day']
                );
            } else {
                unset($licenceVehicle['removalDate']);
            }
        }
        return $licenceVehicle;
    }

    /**
     * Disable removed and specified dates if needed
     *
     * @param Zend\Form\Form $form
     * @param Common\Service\Helper\FormHelper
     */
    public function maybeDisableRemovedAndSpecifiedDates($form, $formHelper)
    {
    }

    /**
     * Unset specified date if needed
     *
     * @param array $data
     * @return array
     */
    public function maybeUnsetSpecifiedDate($data)
    {
        return $data;
    }
}
