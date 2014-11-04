<?php

/**
 * Vehicles PSV Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Service\Entity\LicenceEntityService;
use Common\Service\Entity\VehicleEntityService;
use Common\Form\Elements\Validators\TableRequiredValidator;
use Zend\Form\Form;

/**
 * Vehicles PSV Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractVehiclesPsvController extends AbstractVehiclesController
{
    use Traits\CrudTableTrait;

    protected $section = 'vehicles_psv';

    private $psvTypes = [
        'small'  => VehicleEntityService::PSV_TYPE_SMALL,
        'medium' => VehicleEntityService::PSV_TYPE_MEDIUM,
        'large'  => VehicleEntityService::PSV_TYPE_LARGE
    ];

    /**
     * Simple helper method to extract tables based on
     * available types
     */
    private function getTables()
    {
        return array_keys($this->psvTypes);
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        // we always need this basic data
        // @TODO not in abstract, references 'Application'
        $entityData = $this->getServiceLocator()
            ->get('Entity\Application')
            ->getDataForVehiclesPsv($this->params('id'));

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->formatDataForForm($entityData);
        }

        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $formHelper
            ->createForm('Lva\PsvVehicles')
            ->setData($data);

        // we want to alter based on the *original* entity data, not how
        // it's been manipulated to suit the form (if relevant)
        $form = $this->alterForm($form, $entityData);

        foreach ($this->getTables() as $tableName) {

            // no point wasting time fetching data for a table
            // we've already removed
            if (!$form->has($tableName)) {
                continue;
            }

            $table = $this->getServiceLocator()
                ->get('Table')
                ->prepareTable(
                    'lva-psv-vehicles-' . $tableName,
                    $this->getTableData($tableName)
                );

            $formHelper->populateFormTable(
                $form->get($tableName),
                $table,
                $tableName
            );
        }

        if ($request->isPost() && $form->isValid()) {

            $this->save($form->getData());
            $this->postSave('vehicles_psv');

            $crudAction = $this->getCrudAction($data);

            if ($crudAction !== null) {
                $alternativeCrudResponse = $this->checkForAlternativeCrudAction(
                    $this->getActionFromCrudAction($crudAction)
                );

                if ($alternativeCrudResponse !== null) {
                    return $alternativeCrudResponse;
                }

                // handle the original action as planned
                return $this->handleCrudAction($crudAction);
            }

            return $this->completeSection('vehicles_psv');
        }

        $this->getServiceLocator()->get('Script')->loadFile('vehicle-psv');

        return $this->render('vehicles_psv', $form);
    }

    /**
     * Format data for the main form; not a lot to it
     */
    protected function formatDataForForm($data)
    {
        return array(
            'data' => array(
                'version'       => $data['version'],
                'hasEnteredReg' => $data['hasEnteredReg']
            )
        );
    }

    /**
     * Format data for save on the main form
     */
    protected function formatDataForSave($data)
    {
        return $data['data'];
    }

    protected function save($data)
    {
        $data = $this->formatDataForSave($data);
        $data['id'] = $this->params('id');
        return $this->getLvaEntityService()->save($data);
    }

    /**
     * Override the get crud action method
     *
     * @param array $formTables
     * @return array
     */
    protected function getCrudAction(array $formTables = array())
    {
        $data = $formTables;

        foreach ($this->getTables() as $section) {

            if (isset($data[$section]['action'])) {

                $action = $this->getActionFromCrudAction($data[$section]);

                $data[$section]['routeAction'] = $section . '-' . strtolower($action);

                return $data[$section];
            }
        }

        return null;
    }

    /**
     * Add a small vehicle
     */
    public function smallAddAction()
    {
        return $this->addOrEdit('add', 'small');
    }

    /**
     * Edit a small vehicle
     */
    public function smallEditAction()
    {
        return $this->addOrEdit('edit', 'small');
    }

    /**
     * Delete a small vehicle
     */
    public function smallDeleteAction()
    {
        return $this->deleteAction();
    }

    /**
     * Add a medium vehicle
     */
    public function mediumAddAction()
    {
        return $this->addOrEdit('add', 'medium');
    }

    /**
     * Edit a medium vehicle
     */
    public function mediumEditAction()
    {
        return $this->addOrEdit('edit', 'medium');
    }

    /**
     * Delete a medium vehicle
     */
    public function mediumDeleteAction()
    {
        return $this->deleteAction();
    }

    /**
     * Add a large vehicle
     */
    public function largeAddAction()
    {
        return $this->addOrEdit('add', 'large');
    }

    /**
     * Edit a large vehicle
     */
    public function largeEditAction()
    {
        return $this->addOrEdit('edit', 'large');
    }

    /**
     * Delete a large vehicle
     */
    public function largeDeleteAction()
    {
        return $this->deleteAction();
    }

    /**
     * Helper method to add or edit a vehicle
     * of any size
     */
    protected function addOrEdit($mode, $type)
    {
        $request = $this->getRequest();

        $data = array();
        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            // @NOTE: deliberately an 'else' here, since we want to
            // populate the form on both add *and* edit
            $data = $this->formatVehicleDataForForm(
                $this->getVehicleFormData($this->params('child_id')),
                $type
            );
        }

        $form = $this->getServiceLocator()
            ->get('Helper\Form')
            ->createForm('Lva\PsvVehiclesVehicle');

        $form = $this->alterVehicleForm($form, $mode)
            ->setData($data);

        if ($request->isPost() && $form->isValid()) {

            // If we are in edit mode, we can save
            // If we are in add mode, and we have confirmed add, we can save
            // If we are in add mode, and haven't confirmed add, but the VRM is new we can save
            if ($mode === 'edit'
                || (isset($data['licence-vehicle']['confirm-add']) && !empty($data['licence-vehicle']['confirm-add']))
                || !$this->checkIfVehicleExistsOnOtherLicences($data, $form)
            ) {

                $data = $this->getServiceLocator()->get('Helper\Data')
                    ->processDataMap($form->getData(), $this->vehicleDataMap);

                $this->saveVehicle($data, $mode);

                return $this->handlePostSave();
            }
        }

        return $this->render($mode . '_vehicle', $form);
    }

    /**
     * Remove vehicle size tables based on OC data
     *
     * @param Form $form
     * @return Form
     */
    public function alterForm($form, $data)
    {
        $isPost = $this->getRequest()->isPost();
        $post   = $this->getRequest()->getPost();

        $formHelper = $this->getServiceLocator()
            ->get('Helper\Form');

        $isCrudPressed = (isset($post['large']['action']) && !empty($post['large']['action']))
            || (isset($post['medium']['action']) && !empty($post['medium']['action']))
            || (isset($post['small']['action']) && !empty($post['small']['action']));

        foreach ($this->getTables() as $table) {

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

        $licenceData = $this->getTypeOfLicenceData();
        if ($licenceData['licenceType'] === LicenceEntityService::LICENCE_TYPE_RESTRICTED && $form->has('large')) {
            $formHelper->remove($form, 'large');
        }

        return $form;
    }

    /**
     * Alter action form
     *
     * @param \Zend\Form\Form $form
     * @return Form
     */
    protected function alterVehicleForm($form, $mode)
    {
        $form = parent::alterVehicleForm($form, $mode);

        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        if (!in_array($this->params('action'), array('small-add', 'small-edit'))) {
            $formHelper->remove($form, 'data->isNovelty');
        }

        $formHelper->remove($form, 'licence-vehicle->discNo');

        return $form;
    }

    /**
     * Format vehicle data for form
     */
    protected function formatVehicleDataForForm($data, $type)
    {
        $licenceVehicle = $data;
        unset($data['licenceVehicle']);

        // the main data key wants to be mapped from the vehicle
        // array, but bear in mind it might not exist if we're on 'add'
        $data = isset($data['vehicle']) ? $data['vehicle'] : [];
        // ... the reason we're called on add is we always want psvType
        $data['psvType'] = $this->getPsvTypeFromType($type);

        return array(
            'licence-vehicle' => $licenceVehicle,
            'data'            => $data
        );
    }

    protected function getTableData($table)
    {
        $licenceVehicles = $this->getServiceLocator()->get('Entity\Licence')->getVehiclesPsvData(
            $this->getLicenceId()
        );

        $rows = array();

        $type = $this->getPsvTypeFromType($table);

        foreach ($licenceVehicles as $licenceVehicle) {

            // wrong type (small, medium, large)
            if (!isset($licenceVehicle['vehicle']['psvType']['id'])
                || $licenceVehicle['vehicle']['psvType']['id'] !== $type) {
                continue;
            }

            // wrong visibility (removed)
            if (!$this->showVehicle($licenceVehicle)) {
                continue;
            }

            // flatten data
            $row = array_merge($licenceVehicle, $licenceVehicle['vehicle']);
            unset($row['vehicle']);

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Get a PSV type from type
     *
     * @param string $type
     * @return string|null
     */
    protected function getPsvTypeFromType($type)
    {
        return isset($this->psvTypes[$type]) ? $this->psvTypes[$type] : null;
    }

    /**
     * Get data to populate the vehicle form
     */
    protected function getVehicleFormData($id = null)
    {
        if ($id === null) {
            return array();
        }
        return $this->getServiceLocator()->get('Entity\LicenceVehicle')->getVehiclePsv($id);
    }

    /**
     * Delete vehicles
     */
    protected function delete()
    {
        $licenceVehicleIds = explode(',', $this->params('child_id'));

        foreach ($licenceVehicleIds as $id) {
            $this->getServiceLocator()->get('Entity\LicenceVehicle')->delete($id);
        }
    }

    /**
     * Get the total vehicle authorisations
     *
     * @return int
     */
    protected function getTotalNumberOfAuthorisedVehicles()
    {
        $type = $this->getTypeFromPost();

        if (!isset($this->totalAuthorisedVehicles[$type])) {
            $this->totalAuthorisedVehicles[$type] = $this->getLvaEntityService()->getTotalVehicleAuthorisation(
                $this->params('id'),
                ucfirst($type)
            );
        }

        return $this->totalAuthorisedVehicles[$type];
    }

    /**
     * Get the total number of vehicles
     *
     * @return int
     */
    protected function getTotalNumberOfVehicles()
    {
        $type = $this->getPsvTypeFromType(
            $this->getTypeFromPost()
        );

        if (!isset($this->totalVehicles[$type])) {
            $this->totalVehicles[$type] = $this->getServiceLocator()
                ->get('Entity\Licence')
                ->getVehiclesPsvTotal(
                    $this->getLicenceId(),
                    $type
                );
        }

        return $this->totalVehicles[$type];
    }

    /**
     * Helper so we can always work out what type of PSV we're
     * dealing with from POST data
     */
    protected function getTypeFromPost()
    {
        $data = (array)$this->getRequest()->getPost();

        foreach ($this->getTables() as $type) {
            if (isset($data[$type]['action'])) {
                return $type;
            }
        }
    }
}
