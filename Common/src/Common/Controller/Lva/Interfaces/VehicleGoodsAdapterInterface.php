<?php

/**
 * Vehicle Goods Adapter Interface
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Controller\Lva\Interfaces;

/**
 * Vehicle Goods Adapter Interface
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
interface VehicleGoodsAdapterInterface extends AdapterInterface, VehiclesAdapterInterface
{
    public function save($data, $id);

    public function getFormData($id);

    public function showFilters();

    public function getFilterForm();

    public function getFilters($params);

    /**
     * Disable removed and specified dates if needed
     *
     * @param Zend\Form\Form $form
     * @param Common\Service\Helper\FormHelper
     */
    public function maybeDisableRemovedAndSpecifiedDates($form, $formHelper);

    /**
     * Format removed and specified dates if needed
     *
     * @param array $licenceVehicle
     * @return array
     */
    public function maybeFormatRemovedAndSpecifiedDates($licenceVehicle);

    /**
     * Unset specified date if needed
     *
     * @param array $data
     * @return array
     */
    public function maybeUnsetSpecifiedDate($data);

    /**
     * Don't create an empty option in edit mode for specified date
     *
     * @param Zend\Form\Form $form
     * @param string $mode
     * @return Zend\Form\Form
     */
    public function maybeRemoveSpecifiedDateEmptyOption($form, $mode);
}
