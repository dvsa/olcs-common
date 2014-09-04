<?php

/**
 * Generic Vehicle Section Trait
 *
 * Internal/External - Application/Licence - Vehicle/VehiclePsv Section
 *
 * @NOTE Includes shared logic between ALL Vehicle and VehiclePsv sections, internally/externally, application/licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits;

/**
 * Generic Vehicle Section Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait GenericVehicleSection
{
    /**
     * Save vehicle
     *
     * @param array $data
     * @throws \Exception
     */
    protected function saveVehicle($data, $action)
    {
        $licenceVehicle = $data['licence-vehicle'];
        unset($data['licence-vehicle']);

        $saved = parent::actionSave($data, 'Vehicle');

        if ($action == 'add') {

            if (!isset($saved['id'])) {
                throw new \Exception('Unable to save vehicle');
            }

            $licenceVehicle['vehicle'] = $saved['id'];
            $licenceVehicle['licence'] = $this->getLicenceId();
        } else {
            $licenceVehicle['vehicle'] = $data['id'];
        }

        parent::actionSave($licenceVehicle, 'LicenceVehicle');
    }

    /**
     * Generic form alterations
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function genericActionFormAlterations($form)
    {
        $dataFieldset = $form->get('licence-vehicle');

        $this->disableDateElement($dataFieldset->get('specifiedDate'));
        $this->disableDateElement($dataFieldset->get('deletedDate'));
        $dataFieldset->get('discNo')->setAttribute('disabled', 'disabled');

        return $form;
    }

    /**
     * Disable date element
     *
     * @param \Zend\Form\Element\DateSelect $element
     */
    protected function disableDateElement($element)
    {
        $element->getDayElement()->setAttribute('disabled', 'disabled');
        $element->getMonthElement()->setAttribute('disabled', 'disabled');
        $element->getYearElement()->setAttribute('disabled', 'disabled');
    }

    /**
     * Save the vehicle
     *
     * @param array $data
     * @param string $action
     */
    protected function doActionSave($data, $action)
    {
        if ($action !== 'add') {
            // We don't want these updating
            unset($data['licence-vehicle']['specifiedDate']);
            unset($data['licence-vehicle']['deletedDate']);
            unset($data['licence-vehicle']['discNo']);
        }

        $this->saveVehicle($data, $action);
    }
}
