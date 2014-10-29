<?php

/**
 * Vehicles Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Zend\Form\Form;
use Zend\Form\Element\Checkbox;
use Common\Form\Elements\Validators\NewVrm;

/**
 * Vehicles Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractVehiclesController extends AbstractController
{
    use Traits\CrudTableTrait;

    protected $section = 'vehicles';

    /**
     * Action data map
     *
     * @var array
     */
    protected $vehicleDataMap = array(
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
     * Decide whether to show the vehicle in the table
     *
     * @param array $licenceVehicle
     * @return boolean
     */
    abstract protected function showVehicle(array $licenceVehicle);

    /**
     * Get the total vehicle authorisations
     *
     * @return int
     */
    abstract protected function getTotalNumberOfAuthorisedVehicles();

    /**
     * Redirect to the first section
     *
     * @NOTE as we don't have a form here, we don't need to update completion status, so we don't call postSave
     *
     * @return Response
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $data = (array)$request->getPost();

            $crudAction = $this->getCrudAction(array($data['table']));

            if ($crudAction !== null) {

                if ($this->getActionFromCrudAction($crudAction) === 'add' && !$this->hasVehicleSpaces()) {
                    $this->addErrorMessage('more-vehicles-than-total-auth-error');
                    return $this->reload();
                }

                return $this->handleCrudAction($crudAction);
            }

            return $this->completeSection('vehicles');
        }

        $form = $this->getForm();

        $this->getServiceLocator()->get('Script')->loadFile('lva-crud');

        return $this->render('vehicles', $form);
    }

    /**
     * Check if we have room for any more vehicles
     *
     * @return boolean
     */
    protected function hasVehicleSpaces()
    {
        $totalAuth = $this->getTotalNumberOfAuthorisedVehicles();

        if (is_numeric($totalAuth)) {
            $vehicleCount = $this->getTotalNumberOfVehicles();

            return ($vehicleCount < $totalAuth);
        }

        return true;
    }

    /**
     * Add operating centre
     */
    public function addAction()
    {
        return $this->addOrEdit('add');
    }

    /**
     * Edit operating centre
     */
    public function editAction()
    {
        return $this->addOrEdit('edit');
    }

    /**
     * Add/Edit action
     *
     * @param string $mode
     */
    protected function addOrEdit($mode)
    {
        $request = $this->getRequest();

        $data = array();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } elseif ($mode === 'edit') {
            $data = $this->loadVehicle();
        }

        $form = $this->getVehicleForm($mode)->setData($data);

        // @todo this could do with drying up
        if ($mode !== 'add') {
            $form->get('form-actions')->remove('addAnother');
        }

        if ($request->isPost() && $form->isValid()) {

            $data = $this->getServiceLocator()->get('Helper\Data')->processDataMap($data, $this->vehicleDataMap);

            $data = $this->alterVehicleDataForSave($data);

            $this->saveVehicle($data, $mode);

            return $this->handlePostSave();
        }

        return $this->render($mode . '_vehicles', $form);
    }

    protected function loadVehicle()
    {
        $data = $this->getServiceLocator()->get('Entity\LicenceVehicle')->getVehicle(
            $this->params('child_id')
        );

        $licenceVehicle = $data;

        unset($licenceVehicle['vehicle']);

        $licenceVehicle['discNo'] = $this->getCurrentDiscNo($licenceVehicle);
        unset($licenceVehicle['goodsDiscs']);

        return array(
            'licence-vehicle' => $licenceVehicle,
            'data' => $data['vehicle']
        );
    }

    /**
     * Get current disc number
     *
     * @param array $licenceVehicle
     * @return string
     */
    abstract protected function getCurrentDiscNo($licenceVehicle);

    /**
     * Get the vehicles form
     *
     * @return \Zend\Form\Form
     */
    protected function getForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $formHelper->createForm('Lva\GoodsVehicles');

        $formHelper->populateFormTable($form->get('table'), $this->getVehicleTable());

        return $form;
    }

    /**
     * Get vehicles table
     *
     * @return \Common\Service\Table\TableBuilder
     */
    protected function getVehicleTable()
    {
        $table = $this->getServiceLocator()->get('Table')->prepareTable('lva-vehicles', $this->getVehicleTableData());

        $this->alterTable($table);

        return $table;
    }

    /**
     * This currently doesn't do anything, but is extended by various traits
     *
     * @param \Common\Service\Table\TableBuilder
     */
    protected function alterTable($table)
    {

    }

    /**
     * Get vehicle table data
     *
     * @return array
     */
    protected function getVehicleTableData()
    {
        $vehicles = $this->getServiceLocator()->get('Entity\Licence')->getVehiclesData($this->getLicenceId());

        return $this->formatTableData($vehicles);
    }

    /**
     * Format table data
     *
     * @param array $data
     * @return array
     */
    protected function formatTableData($data)
    {
        $results = array();

        if (isset($data['licenceVehicles']) && !empty($data['licenceVehicles'])) {

            foreach ($data['licenceVehicles'] as $licenceVehicle) {

                if (!$this->showVehicle($licenceVehicle)) {
                    continue;
                }

                $row = array_merge($licenceVehicle, $licenceVehicle['vehicle']);

                unset($row['vehicle']);
                unset($row['goodsDiscs']);

                $row['discNo'] = $this->getCurrentDiscNo($licenceVehicle);

                $results[] = $row;
            }
        }

        return $results;
    }

    /**
     * Get the add/edit vehicle form
     *
     * @return \Zend\Form\Form
     */
    protected function getVehicleForm($mode)
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $form = $formHelper->createForm('Lva\GoodsVehiclesVehicle');

        $this->alterVehicleForm($form);

        $dataFieldset = $form->get('licence-vehicle');

        $formHelper->disableDateElement($dataFieldset->get('specifiedDate'));
        $formHelper->disableDateElement($dataFieldset->get('removalDate'));

        $dataFieldset->get('discNo')->setAttribute('disabled', 'disabled');

        if ($mode == 'edit') {
            $filter = $form->getInputFilter();
            $filter->get('data')->get('vrm')->setAllowEmpty(true);
            $filter->get('data')->get('vrm')->setRequired(false);
            $form->get('data')->get('vrm')->setAttribute('disabled', 'disabled');
        }

        if ($mode == 'add' && $this->getRequest()->isPost()) {
            $filter = $form->getInputFilter();
            $validators = $filter->get('data')->get('vrm')->getValidatorChain();

            $validators->attach($this->getNewVrmValidator());
        }

        return $form;
    }

    /**
     * This method currently doesn't do anything, but is extended in various traits
     *
     * @param Form $form
     */
    protected function alterVehicleForm(Form $form)
    {

    }

    protected function getNewVrmValidator()
    {
        $validator = new NewVrm();

        $validator->setType('Application');
        $validator->setVrms($this->getVrmsForCurrentLicence());

        return $validator;
    }

    /**
     * Get vrms linked to licence
     *
     * @return array
     */
    protected function getVrmsForCurrentLicence()
    {
        return $this->getServiceLocator()->get('Entity\Licence')->getCurrentVrms($this->getLicenceId());
    }

    /**
     * Save the vehicle
     *
     * @param array $data
     * @param string $mode
     */
    protected function saveVehicle($data, $mode)
    {
        if ($mode !== 'add') {
            // We don't want these updating
            unset($data['licence-vehicle']['specifiedDate']);
            unset($data['licence-vehicle']['removalDate']);
            unset($data['licence-vehicle']['discNo']);
            unset($data['vrm']);
        }

        $licenceVehicle = $data['licence-vehicle'];
        unset($data['licence-vehicle']);

        if ($mode == 'add') {
            unset($data['id']);
            unset($data['version']);
        }

        $saved = $this->getServiceLocator()->get('Entity\Vehicle')->save($data);

        if ($mode == 'add') {
            $licenceVehicle['vehicle'] = $saved['id'];
            $licenceVehicle['licence'] = $this->getLicenceId();
        } else {
            $licenceVehicle['vehicle'] = $data['id'];
        }

        $this->getServiceLocator()->get('Entity\LicenceVehicle')->save($licenceVehicle);
    }

    /**
     * This by default doesn't do anything, but is extended in various traits
     *
     * @param array $data
     * @return array
     */
    protected function alterVehicleDataForSave(array $data)
    {
        return $data;
    }

    /**
     * Cease and delete the vehicle
     */
    protected function delete()
    {
        $ids = explode(',', $this->params('child_id'));

        $licenceVehicleService = $this->getServiceLocator()->get('Entity\LicenceVehicle');

        foreach ($ids as $id) {

            $licenceVehicleService->ceaseActiveDisc($id);

            $licenceVehicleService->delete($id);
        }
    }

    /**
     * Get the total number of vehicles
     *
     * @return int
     */
    protected function getTotalNumberOfVehicles()
    {
        return $this->getServiceLocator()->get('Entity\Licence')
            ->getVehiclesTotal($this->getLicenceId());
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
}
