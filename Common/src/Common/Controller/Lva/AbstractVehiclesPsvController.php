<?php

/**
 * Vehicles PSV Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva;

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

    private $tables = ['small', 'medium', 'large'];

    private $psvTypes = [
        'small' => 'vhl_t_a',
        'medium' => 'vhl_t_b',
        'large' => 'vhl_t_c'
    ];

    /**
     * Index action
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->formatDataForForm($this->getFormData());
        }

        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $formHelper
            ->createForm('Lva\PsvVehicles')
            ->setData($data);

        foreach ($this->tables as $tableName) {
            $table = $this->getServiceLocator()
                ->get('Table')
                ->prepareTable(
                    'lva-psv-vehicles-' . $tableName,
                    // @TODO data
                    $this->getTableData($tableName)
                );

            $formHelper->populateFormTable(
                $form->get($tableName),
                $table,
                $tableName
            );
        }

        if ($request->isPost()) {

            $crudAction = $this->getCrudAction($data);

            // @TODO re-implement checkForAlternativeCrudAction
            // and limit 'add' based on total authed vehicles

            /*
            if ($crudAction !== null) {
                $this->getServiceLocator()->get('Helper\Form')->disableEmptyValidation($form);
            }
             */

            if ($form->isValid()) {

                $this->save($data);

                $this->postSave('vehicles_psv');

                if ($crudAction !== null) {
                    return $this->handleCrudAction($crudAction);
                }

                return $this->completeSection('vehicles_psv');
            }
        }

        $this->getServiceLocator()->get('Script')->loadFile('vehicle-psv');

        return $this->render('vehicles_psv', $form);
    }

    protected function getFormData()
    {
        // @TODO not in abstract, references 'Application'
        return $this->getServiceLocator()
            ->get('Entity\Application')
            ->getDataForVehiclesPsv($this->params('id'));
    }

    protected function formatDataForForm($data)
    {
        return array(
            'data' => array(
                'version' => $data['version'],
                'hasEnteredReg' => $data['hasEnteredReg']
            )
        );
    }

    protected function formatDataForSave($data)
    {
        return $data['data'];
    }

    protected function save($data)
    {
        $data = $this->formatDataForSave($data);
        // @TODO remove ref to 'Application'
        $data['id'] = $this->params('id');
        return $this->getServiceLocator()->get('Entity\Application')->save($data);
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

        foreach ($this->tables as $section) {

            if (isset($data[$section]['action'])) {

                $action = $this->getActionFromCrudAction($data[$section]);

                $data[$section]['routeAction'] = $section . '-' . strtolower($action);

                return $data[$section];
            }
        }

        return null;
    }

    public function smallAddAction()
    {
        return $this->addOrEdit('add', 'small');
    }

    public function smallEditAction()
    {
        return $this->addOrEdit('edit', 'small');
    }

    public function mediumAddAction()
    {
        return $this->addOrEdit('add', 'medium');
    }

    public function mediumEditAction()
    {
        return $this->addOrEdit('edit', 'medium');
    }

    public function largeAddAction()
    {
        return $this->addOrEdit('add', 'large');
    }

    public function largeAction()
    {
        return $this->addOrEdit('edit', 'large');
    }

    protected function addOrEdit($mode, $type)
    {
        $request = $this->getRequest();

        $data = array();
        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->formatVehicleDataForForm(
                $this->getVehicleFormData($this->params('child_id')),
                $type
            );
        }

        $form = $this->getServiceLocator()
            ->get('Helper\Form')
            ->createForm('Lva\PsvVehiclesVehicle');

        $form = $this->alterVehicleForm($form, $mode);

        $form->setData($data);

        if ($request->isPost() && $form->isValid()) {
            // If we are in edit mode, we can save
            // If we are in add mode, and we have confirmed add, we can save
            // If we are in add mode, and haven't confirmed add, but the VRM is new we can save
            if ($mode === 'edit'
                || (isset($data['licence-vehicle']['confirm-add']) && !empty($data['licence-vehicle']['confirm-add']))
                || !$this->checkIfVehicleExistsOnOtherLicences($data, $form)
            ) {

                $data = $data = $this->getServiceLocator()->get('Helper\Data')
                    ->processDataMap($data, $this->vehicleDataMap);

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
    public function doAlterForm($form)
    {
        // @TODO re-implement!
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
            $form->getInputFilter()->remove('large');
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

    protected function formatVehicleDataForForm($data, $type)
    {
        $licenceVehicle = $data;
        unset($data['licenceVehicle']);

        $data = isset($data['vehicle']) ? $data['vehicle'] : [];
        $data['psvType'] = $this->getPsvTypeFromType($type);

        return array(
            'licence-vehicle' => $licenceVehicle,
            'data' => $data
        );
    }

    protected function getTableData($table)
    {
        $licenceId = $this->getLicenceId();

        $licenceVehicles = $this->getServiceLocator()->get('Entity\Licence')->getVehiclesPsvData($licenceId);

        $rows = array();

        $type = $this->getPsvTypeFromType($table);

        foreach ($licenceVehicles as $licenceVehicle) {

            if (!isset($licenceVehicle['vehicle']['psvType']['id'])
                || $licenceVehicle['vehicle']['psvType']['id'] != $type) {
                continue;
            }

            if (!$this->showVehicle($licenceVehicle)) {
                continue;
            }

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

    protected function getVehicleFormData($id = null)
    {
        if ($id === null) {
            return [];
        }
        return $this->getServiceLocator()->get('Entity\LicenceVehicle')->getVehiclePsv($id);
    }
}
