<?php

/**
 * VehiclePsv Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits;

use Common\Form\Elements\Validators\TableRequiredValidator;

/**
 * VehiclePsv Section
 *
 * @NOTE this trait uses functionality defined in the GenericLicenceSection trait
 *  I would use the trait in here, however using the trait in the implementing class gives us the ability
 *  to opt into an alternative trait with the same interface but with an alternative method of doing the same thing
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait VehiclePsvSection
{
    use GenericVehicleSection;

    /**
     * Action service
     *
     * @var string
     */
    protected $sharedActionService = 'LicenceVehicle';

    /**
     * Shared action data bundle
     *
     * @var array
     */
    protected $sharedActionDataBundle = array(
        'properties' => array(
            'id',
            'version',
            'receivedDate',
            'deletedDate',
            'specifiedDate'
        ),
        'children' => array(
            'vehicle' => array(
                'properties' => array(
                    'id',
                    'version',
                    'vrm',
                    'makeModel',
                    'isNovelty'
                ),
                'children' => array(
                    'psvType' => array(
                        'properties' => array(
                            'id'
                        )
                    )
                )
            )
        )
    );

    /**
     * Action data map
     *
     * @var array
     */
    protected $sharedActionDataMap = array(
        'main' => array(
            'mapFrom' => array(
                'data'
            ),
            'children' => array(
                'licence-vehicle' => array(
                    'mapFrom' => array(
                        'licence-vehicle'
                    )
                )
            )
        )
    );

    /**
     * Holds the form tables
     *
     * @var array
     */
    protected $sharedFormTables = array(
        'small' => 'application_vehicle-safety_vehicle-psv-small',
        'medium' => 'application_vehicle-safety_vehicle-psv-medium',
        'large' => 'application_vehicle-safety_vehicle-psv-large'
    );

    /**
     * Get action service
     *
     * @return string
     */
    protected function getActionService()
    {
        return $this->sharedActionService;
    }

    /**
     * Get action data bundle
     *
     * @return array
     */
    protected function getActionDataBundle()
    {
        return $this->sharedActionDataBundle;
    }

    /**
     * Get action data map
     *
     * @return array
     */
    protected function getActionDataMap()
    {
        return $this->sharedActionDataMap;
    }

    /**
     * Get form tables
     *
     * @return array
     */
    protected function getFormTables()
    {
        return $this->sharedFormTables;
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
     * Add large vehicles
     *
     * @return Response
     */
    public function largeAddAction()
    {
        return $this->renderSection();
    }

    /**
     * Edit large vehicles
     *
     * @return Response
     */
    public function largeEditAction()
    {
        return $this->renderSection();
    }

    /**
     * Delete large vehicle
     *
     * @return Response
     */
    public function largeDeleteAction()
    {
        return $this->delete();
    }

    /**
     * Add medium vehicles
     *
     * @return Response
     */
    public function mediumAddAction()
    {
        return $this->renderSection();
    }

    /**
     * Edit medium vehicles
     *
     * @return Response
     */
    public function mediumEditAction()
    {
        return $this->renderSection();
    }

    /**
     * Delete medium vehicle
     *
     * @return Response
     */
    public function mediumDeleteAction()
    {
        return $this->delete();
    }

    /**
     * Add small vehicles
     *
     * @return Response
     */
    public function smallAddAction()
    {
        return $this->renderSection();
    }

    /**
     * Edit small vehicles
     *
     * @return Response
     */
    public function smallEditAction()
    {
        return $this->renderSection();
    }

    /**
     * Delete small vehicle
     *
     * @return Response
     */
    public function smallDeleteAction()
    {
        return $this->delete();
    }

    /**
     * Alter action form
     *
     * @param Form $form
     * @return Form
     */
    public function alterActionForm($form)
    {
        $form = $this->genericActionFormAlterations($form);

        $actionName = $this->getActionName();

        if (!in_array($actionName, array('small-add', 'small-edit'))) {
            $form->get('data')->remove('isNovelty');
            $form->get('data')->remove('makeModel');
        }

        return $form;
    }

    /**
     * Remove vehicle size tables based on OC data
     *
     * @param Form $form
     * @return Form
     */
    public function doAlterForm($form)
    {
        $data = $this->load($this->getIdentifier());

        $isPost = $this->getRequest()->isPost();
        $post = $this->getRequest()->getPost();

        $isCrudPressed = (isset($post['large']['action']) && !empty($post['large']['action']))
            || (isset($post['medium']['action']) && !empty($post['medium']['action']))
            || (isset($post['small']['action']) && !empty($post['small']['action']));

        foreach (array_keys($this->getFormTables()) as $table) {

            $ucTable = ucwords($table);

            if (isset($data['totAuth' . $ucTable . 'Vehicles']) && $data['totAuth' . $ucTable . 'Vehicles'] < 1) {

                $form->remove($table);

            } elseif (
                !$isCrudPressed && $isPost
                && isset($post['data']['hasEnteredReg']) && $post['data']['hasEnteredReg'] == 'Y'
            ) {
                $input = $form->getInputFilter()->get($table)->get('table');
                $input->setRequired(true)->setAllowEmpty(false)->setContinueIfEmpty(true);

                $validatorChain = $input->getValidatorChain();
                $validatorChain->attach(new TableRequiredValidator(array('label' => $table . ' vehicle')));
            }
        }

        if ($this->getLicenceType() == self::LICENCE_TYPE_RESTRICTED && $form->has('large')) {

            $form->remove('large');
        }

        return $form;
    }

    /**
     * Return the form table data
     *
     * @return array
     */
    protected function formatTableData($data, $table)
    {
        $rows = array();

        $type = $this->getPsvTypeFromType($table);

        foreach ($data['licence']['licenceVehicles'] as $licenceVehicle) {

            if (!isset($licenceVehicle['vehicle']['psvType']['id'])
                || $licenceVehicle['vehicle']['psvType']['id'] != $type) {
                continue;
            }

            if (!$this->showVehicle($licenceVehicle['vehicle'])) {
                continue;
            }

            $row = array_merge($licenceVehicle, $licenceVehicle['vehicle']);
            unset($row['vehicle']);

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * This is extended in the licence section
     *
     * @param array $vehicle
     * @return boolean
     */
    protected function showVehicle($vehicle)
    {
        return true;
    }

    /**
     * Save the vehicle
     *
     * @param array $data
     * @param string $service
     */
    protected function actionSave($data, $service = null)
    {
        $parts = explode('-', $this->getActionName());

        $action = array_pop($parts);

        $this->saveVehicle($data, $action);
    }

    /**
     * Process load
     *
     * @param array $data
     * @return array
     */
    protected function processLoad($data)
    {
        return array('data' => $data);
    }

    /**
     * Process action load
     *
     * @param array $data
     * @return array
     */
    protected function processActionLoad($data)
    {
        $parts = explode('-', $this->getActionName());

        $type = array_shift($parts);

        $licenceVehicle = $data;
        unset($licenceVehicle['vehicle']);

        $data['vehicle']['psvType'] = $this->getPsvTypeFromType($type);

        $data = array(
            'licence-vehicle' => $licenceVehicle,
            'data' => $data['vehicle']
        );

        return $data;
    }

    /**
     * Get a PSV type from type
     *
     * @param string $type
     * @return string|null
     */
    protected function getPsvTypeFromType($type)
    {
        switch ($type) {
            case 'large':
                return 'vhl_t_c';
            case 'medium':
                return 'vhl_t_b';
            case 'small':
                return 'vhl_t_a';
        }

        return null;
    }
}
