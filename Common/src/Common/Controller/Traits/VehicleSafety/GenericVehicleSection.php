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

        // @todo this may be changing after speaking with Same
        // If we are adding the vehicle, we need to check if the vehicle already exists on another licence,
        //  if it does, we need to display a message asking the user to confirm
        /**if ($action == 'add') {
            // Check if the vehicle exists on another licence
            $vrm = $data['vrm'];

            $confirm = new \Zend\Form\Element\Checkbox('confirm-add', array('label' => 'Continue adding vehicle'));

            $form->get('licence-vehicle')->add($confirm);

            $form->setMessages(
                array(
                    'licence-vehicle' => array(
                        'confirm-add' => array(
                            'This vehicle is specified on another licence. Should you wish to continue check this box.'
                        )
                    )
                )
            );
            return true;
        }*/

        return false;
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
