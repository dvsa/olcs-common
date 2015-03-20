<?php

/**
 * Licence Vehicles Goods Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Interfaces\VehicleGoodsAdapterInterface;

/**
 * Licence Vehicles Goods Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceVehiclesGoodsAdapter extends AbstractAdapter implements VehicleGoodsAdapterInterface
{
    public function save($data, $id)
    {
    }

    public function getFormData($id)
    {
        return [];
    }

    /**
     * Get vehicles data for the given resource
     *
     * @param int $id
     * @return array
     */
    public function getVehiclesData($id)
    {
        return $this->getServiceLocator()->get('Entity\Licence')->getVehiclesData($id);
    }

    /**
     * Do we need to show filters for vehciles
     */
    public function showFilters()
    {
        return true;
    }

    /**
     * Retrieve the filter form
     *
     * Here we wrap the application version before removing
     * the irrelevant specified date field
     */
    public function getFilterForm()
    {
        $form = $this->getServiceLocator()->get('ApplicationVehiclesGoodsAdapter')->getFilterForm();

        $this->getServiceLocator()->get('Helper\Form')->remove($form, 'specified');

        return $form;
    }

    /**
     * Get all relevant form filters
     *
     * Here we wrap the application version and override
     * the specified date
     */
    public function getFilters($params)
    {
        return array_merge(
            $this->getServiceLocator()->get('ApplicationVehiclesGoodsAdapter')->getFilters($params),
            ['specified' => 'Y']
        );
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
