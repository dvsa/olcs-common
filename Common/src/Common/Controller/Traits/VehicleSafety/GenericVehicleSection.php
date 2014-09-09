<?php

/**
 * Generic Vehicle Section Trait
 *
 * Internal/External - Application/Licence - Vehicle/VehiclePsv Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\VehicleSafety;

use Common\Form\Elements\Validators\NewVrm;

/**
 * Generic Vehicle Section Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait GenericVehicleSection
{
    /**
     * Check whether we should skip saving
     *
     * @param array $data
     * @param \Zend\Form\Form $form
     */
    protected function shouldSkipActionSave($data, $form)
    {
        $parts = explode('-', $this->getActionName());

        $action = array_pop($parts);

        // If we are adding the vehicle, we need to check if the vehicle already exists on another licence,
        //  if it does, we need to display a message asking the user to confirm
        if ($action == 'add') {

            $post = (array)$this->getRequest()->getPost();

            if (!isset($post['licence-vehicle']['confirm-add']) || empty($post['licence-vehicle']['confirm-add'])) {
                $licences = $this->getOthersLicencesFromVrm($data['vrm'], $this->getLicenceId());

                if (!empty($licences)) {
                    $confirm = new \Zend\Form\Element\Checkbox(
                        'confirm-add',
                        array(
                            'label' => 'I confirm that I would like to continue adding this vehicle'
                        )
                    );

                    // @todo For some reason this message doesn't appear in the FormErrors view helper
                    // @todo Need to also change this message for external users
                    $confirm->setMessages(
                        array(
                            'This vehicle is specified on another licence: ' . implode(', ', $licences) . '. Please confirm you would like to continue adding this vehicle'
                        )
                    );

                    $form->get('licence-vehicle')->add($confirm);

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get a list of licences that have this vehicle (Except the current licence)
     *
     * @param string $vrm
     * @param int $licenceId
     */
    protected function getOthersLicencesFromVrm($vrm, $licenceId)
    {
        $bundle = array(
            'properties' => array(),
            'children' => array(
                'licenceVehicles' => array(
                    'properties' => array(),
                    'children' => array(
                        'licence' => array(
                            'properties' => array(
                                'id',
                                'licNo'
                            )
                        )
                    )
                )
            )
        );

        $results = $this->makeRestCall('vehicle', 'GET', array('vrm' => $vrm), $bundle);

        $licences = array();

        foreach ($results['Results'] as $vehicle) {
            foreach ($vehicle['licenceVehicles'] as $licenceVehicle) {
                if (isset($licenceVehicle['licence']['id']) && $licenceVehicle['licence']['id'] != $licenceId) {
                    $licences[] = $licenceVehicle['licence']['licNo'];
                }
            }
        }

        return $licences;
    }

    /**
     * Save vehicle
     *
     * @param array $data
     * @throws \Exception
     */
    protected function saveVehicle($data, $action)
    {
        $licenceId = $this->getLicenceId();

        $licenceVehicle = $data['licence-vehicle'];
        unset($data['licence-vehicle']);

        $saved = parent::actionSave($data, 'Vehicle');

        if ($action == 'add') {

            if (!isset($saved['id'])) {
                throw new \Exception('Unable to save vehicle');
            }

            $licenceVehicle['vehicle'] = $saved['id'];
            $licenceVehicle['licence'] = $licenceId;
        } else {
            $licenceVehicle['vehicle'] = $data['id'];
        }

        return parent::actionSave($licenceVehicle, 'LicenceVehicle');
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

        $action = $this->getActionName();

        $parts = explode('-', $action);

        $action = array_pop($parts);

        if ($action == 'edit') {
            $filter = $form->getInputFilter();
            $filter->get('data')->get('vrm')->setAllowEmpty(true);
            $filter->get('data')->get('vrm')->setRequired(false);
            $form->get('data')->get('vrm')->setAttribute('disabled', 'disabled');
        }

        if ($action == 'add' && $this->getRequest()->isPost()) {
            $filter = $form->getInputFilter();
            $validators = $filter->get('data')->get('vrm')->getValidatorChain();

            $validator = new NewVrm();

            $validator->setType($this->sectionType);
            $validator->setVrms($this->getVrmsForCurrentLicence());

            $validators->attach($validator);
        }

        return $form;
    }

    /**
     * Get vrms linked to licence
     *
     * @return array
     */
    protected function getVrmsForCurrentLicence()
    {
        $bundle = array(
            'properties' => array(),
            'children' => array(
                'vehicle' => array(
                    'properties' => array('vrm')
                )
            )
        );

        $data = $this->makeRestCall('LicenceVehicle', 'GET', array('licence' => $this->getLicenceId()), $bundle);

        $vrms = array();

        foreach ($data['Results'] as $row) {
            $vrms[] = $row['vehicle']['vrm'];
        }

        return $vrms;
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
            unset($data['vrm']);
        }

        if ($this->sectionType == 'Application') {
            $data = $this->alterDataForApplication($data);
        }

        return $this->saveVehicle($data, $action);
    }

    /**
     * Get the total vehicle authorisations
     *
     * @return int
     */
    protected function getTotalNumberOfAuthorisedVehicles($type = '')
    {
        $type = ucwords($type);

        $bundle = array(
            'properties' => array(
                'totAuth' . $type . 'Vehicles'
            )
        );

        $data = $this->makeRestCall(
            $this->sectionType,
            'GET',
            array('id' => $this->getIdentifier()),
            $bundle
        );
        return $data['totAuth' . $type . 'Vehicles'];
    }
}
