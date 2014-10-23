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
use Zend\Form\Element\Checkbox;

/**
 * Generic Vehicle Section Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait GenericVehicleSection
{
    protected function getNoActionIdentifierRequired()
    {
        return array('add', 'vehicles');
    }

    /**
     * Redirect to the first section
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->renderSection();
    }

    /**
     * Performs delete action
     */
    public function deleteAction()
    {
        return $this->renderSection();
    }

    /**
     * Check whether we should skip saving
     *
     * If we are adding the vehicle, we need to check if the vehicle already exists on another licence,
     *  if it does, we need to display a message asking the user to confirm
     * @param array $data
     * @param \Zend\Form\Form $form
     */
    protected function shouldSkipActionSave($data, $form)
    {
        $action = $this->getActionFromFullActionName();

        if ($action == 'add') {

            $post = (array)$this->getRequest()->getPost();

            if (!isset($post['licence-vehicle']['confirm-add']) || empty($post['licence-vehicle']['confirm-add'])) {

                return $this->checkIfVehicleExistsOnOtherLicences($data, $form);
            }
        }

        return false;
    }

    /**
     * If vehicle exists on another licence, add a message and confirmation field to the form
     *
     * @param array $data
     * @param \Zend\Form\Form $form
     * @return boolean
     */
    protected function checkIfVehicleExistsOnOtherLicences($data, $form)
    {
        $licences = $this->getOthersLicencesFromVrm($data['vrm'], $this->getLicenceId());

        if (!empty($licences)) {

            $confirm = new Checkbox('confirm-add', array('label' => 'vehicle-belongs-to-another-licence-confirmation'));

            $confirm->setMessages(array($this->getErrorMessageForVehicleBelongingToOtherLicences($licences)));

            $form->get('licence-vehicle')->add($confirm);

            return true;
        }

        return false;
    }

    /**
     * We need to manually translate the message, as we need to optionally display a licence number
     * Based on whether we are internal or external
     *
     * @param array $licences
     * @return string
     */
    protected function getErrorMessageForVehicleBelongingToOtherLicences($licences)
    {
        $translator = $this->getServiceLocator()->get('translator');

        $translationKey = 'vehicle-belongs-to-another-licence-message-' . strtolower($this->sectionLocation);

        if ($this->sectionLocation == 'Internal') {

            if (count($licences) > 1) {
                $translationKey .= '-multiple';
            }

            return sprintf($translator->translate($translationKey), implode(', ', $licences));
        }

        return $translator->translate($translationKey);
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
                            ),
                            'children' => array(
                                'applications' => array(
                                    'properties' => array(
                                        'id'
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );

        $results = $this->makeRestCall('Vehicle', 'GET', array('vrm' => $vrm), $bundle);

        $licences = array();

        foreach ($results['Results'] as $vehicle) {
            foreach ($vehicle['licenceVehicles'] as $licenceVehicle) {
                if (isset($licenceVehicle['licence']['id'])
                    && $licenceVehicle['licence']['id'] != $licenceId) {

                    $licenceNumber = 'UNKNOWN';

                    if (empty($licenceVehicle['licence']['licNo'])
                        && isset($licenceVehicle['licence']['applications'][0])) {
                        $licenceNumber = 'APP-' . $licenceVehicle['licence']['applications'][0]['id'];
                    } else {
                        $licenceNumber = $licenceVehicle['licence']['licNo'];
                    }

                    $licences[] = $licenceNumber;
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

        $saved = $this->parentActionSave($data, 'Vehicle');

        if ($action == 'add') {

            if (!isset($saved['id'])) {
                throw new \Exception('Unable to save vehicle');
            }

            $licenceVehicle['vehicle'] = $saved['id'];
            $licenceVehicle['licence'] = $licenceId;
        } else {
            $licenceVehicle['vehicle'] = $data['id'];
        }

        return $this->parentActionSave($licenceVehicle, 'LicenceVehicle');
    }

    /**
     * Generic form alterations
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function genericActionFormAlterations($form)
    {
        $action = $this->getActionFromFullActionName();

        $dataFieldset = $form->get('licence-vehicle');

        $this->disableDateElement($dataFieldset->get('specifiedDate'));
        $this->disableDateElement($dataFieldset->get('deletedDate'));
        $dataFieldset->get('discNo')->setAttribute('disabled', 'disabled');

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
            'properties' => array('removalDate'),
            'children' => array(
                'vehicle' => array(
                    'properties' => array('vrm')
                )
            )
        );

        $data = $this->makeRestCall('LicenceVehicle', 'GET', array('licence' => $this->getLicenceId()), $bundle);

        $vrms = array();

        foreach ($data['Results'] as $row) {
            if (!$row['removalDate']) {
                $vrms[] = $row['vehicle']['vrm'];
            }
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

    /**
     * Alter delete form
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function alterDeleteForm($form)
    {
        $form->get('data')->get('id')->setLabel('vehicle-remove-confirm-label');

        return $form;
    }
}
