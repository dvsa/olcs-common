<?php

/**
 * Vehicles Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Form\Elements\Validators\NewVrm;
use Zend\Form\Element\Checkbox;

/**
 * Vehicles Trait
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
     * We need to know which vehicles to show
     *
     * @param array $licenceVehicle
     * @return boolean
     */
    abstract protected function showVehicle(array $licenceVehicle);

    /**
     * Redirect to the first section
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

                $action = $this->getActionFromCrudAction($crudAction);

                $alternativeCrudAction = $this->checkForAlternativeCrudAction($action);

                if ($alternativeCrudAction === null) {
                    return $this->handleCrudAction($crudAction);
                }

                return $alternativeCrudAction;
            }

            return $this->completeSection('vehicles');
        }

        $form = $this->getForm();

        return $this->render('vehicles', $form);
    }

    /**
     * Hijack the crud action check so we can validate the add button
     *
     * @param string $action
     */
    protected function checkForAlternativeCrudAction($action)
    {
        if ($action == 'reprint') {
            $post = (array)$this->getRequest()->getPost();

            $id = $post['table']['id'];

            if ($this->isDiscPendingForLicenceVehicle($id)) {

                $this->getServiceLocator()->get('Helper\FlashMessenger')
                    ->addErrorMessage('reprint-pending-disc-error');

                return $this->reload();
            }
        }

        if ($action == 'add') {
            $totalAuth = $this->getTotalNumberOfAuthorisedVehicles();

            if (!is_numeric($totalAuth)) {
                return;
            }

            $vehicleCount = $this->getTotalNumberOfVehicles();

            if ($vehicleCount >= $totalAuth) {

                $this->getServiceLocator()->get('Helper\FlashMessenger')
                    ->addErrorMessage('more-vehicles-than-total-auth-error');

                return $this->reload();
            }
        }
    }

    /**
     * Get the total vehicle authorisations
     *
     * @return int
     */
    protected function getTotalNumberOfAuthorisedVehicles()
    {
        return $this->getLvaEntityService()->getTotalAuthorisation($this->params('id'));
    }

    /**
     * Get total number of vehicles
     *
     * @return int
     */
    protected function getTotalNumberOfVehicles()
    {
        return $this->getServiceLocator()->get('Entity\Licence')->getVehiclesTotal($this->getLicenceId());
    }

    /**
     * Check if the licence vehicle has a pending active disc
     *
     * @param int $id
     * @return boolean
     */
    protected function isDiscPendingForLicenceVehicle($id)
    {
        $ids = (array)$id;

        foreach ($ids as $id) {
            $results = $this->getServiceLocator()->get('Entity\LicenceVehicle')->getDiscPendingData($id);

            if ($this->isDiscPending($results)) {
                return true;
            }
        }

        return false;
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

    protected function addOrEdit($mode)
    {
        $request = $this->getRequest();
        $id = $this->params('id');
        $data = array();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } elseif ($mode === 'edit') {
            $data = $this->formatVehicleDataForForm($this->getVehicleFormData($id));
        }

        $form = $this->alterVehicleForm($this->getVehicleForm()->setData($data), $mode);

        if ($mode === 'edit') {
            $form->get('form-actions')->remove('addAnother');
        }

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

        return $this->render($mode . '_vehicles', $form);
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
            unset($data['licence-vehicle']['deletedDate']);
            unset($data['licence-vehicle']['discNo']);
            unset($data['vrm']);
        }

        // @todo This needs implementing some other way
//        if ($this->sectionType == 'Application') {
//            $data = $this->alterDataForApplication($data);
//        }

        $licenceVehicle = $data['licence-vehicle'];
        unset($data['licence-vehicle']);

        if (checkdate(
            (int)$licenceVehicle['receivedDate']['month'],
            (int)$licenceVehicle['receivedDate']['day'],
            (int)$licenceVehicle['receivedDate']['year']
        )) {
            $licenceVehicle['receivedDate'] = sprintf(
                '%s-%s-%s',
                $licenceVehicle['receivedDate']['year'],
                $licenceVehicle['receivedDate']['month'],
                $licenceVehicle['receivedDate']['day']
            );
        } else {
            unset($licenceVehicle['receivedDate']);
        }

        $saved = $this->getServiceLocator()->get('Entity\Vehicle')->save($data);

        if ($mode == 'add') {

            if (!isset($saved['id'])) {
                // @todo replace with a different exception
                throw new \Exception('Unable to save vehicle');
            }

            $licenceVehicle['vehicle'] = $saved['id'];
            $licenceVehicle['licence'] = $this->getLicenceId();
        } else {
            $licenceVehicle['vehicle'] = $data['id'];
        }

        $this->getServiceLocator()->get('Entity\LicenceVehicle')->save($licenceVehicle);
    }

    protected function getVehicleFormData($id)
    {
        return $this->getServiceLocator()->get('Entity\LicenceVehicle')->getVehicle($id);
    }

    /**
     * Format the data for the form
     *
     * @param array $data
     * @return array
     */
    protected function formatVehicleDataForForm($data)
    {
        $licenceVehicle = $data;
        unset($licenceVehicle['vehicle']);

        $licenceVehicle['discNo'] = $this->getCurrentDiscNo($licenceVehicle);
        unset($licenceVehicle['goodsDiscs']);

        return array(
            'licence-vehicle' => $licenceVehicle,
            'data' => $data['vehicle']
        );
    }

    protected function getVehicleForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\GoodsVehiclesVehicle');
    }

    /**
     * Generic form alterations
     *
     * @param \Zend\Form\Form $form
     * @param string $mode
     * @return \Zend\Form\Form
     */
    protected function alterVehicleForm($form, $mode)
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $dataFieldset = $form->get('licence-vehicle');

        $formHelper->disableDateElement($dataFieldset->get('specifiedDate'));
        $formHelper->disableDateElement($dataFieldset->get('removalDate'));

        $dataFieldset->get('discNo')->setAttribute('disabled', 'disabled');

        // disable the vrm field on edit
        if ($mode === 'edit') {
            $formHelper->disableElement($form, 'data->vrm');
        }

        // Attach a validator to check the VRM doesn't already exist
        // We only really need to do this when posting
        if ($mode === 'add' && $this->getRequest()->isPost()) {

            $filter = $form->getInputFilter();
            $validators = $filter->get('data')->get('vrm')->getValidatorChain();

            $validator = new NewVrm();

            $validator->setType(ucwords($this->lva));
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
        return $this->getServiceLocator()->get('Entity\Licence')->getCurrentVrms($this->getLicenceId());
    }

    protected function getForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $formHelper->createForm('Lva\GoodsVehicles');

        $formHelper->populateFormTable($form->get('table'), $this->getTable());

        return $form;
    }

    protected function getTable()
    {
        return $this->getServiceLocator()->get('Table')->prepareTable('lva-vehicles', $this->getTableData());
    }

    protected function getTableData()
    {
        $licenceId = $this->getLicenceId();

        $licenceVehicles = $this->getServiceLocator()->get('Entity\Licence')->getVehiclesData($licenceId);

        $results = array();

        foreach ($licenceVehicles as $licenceVehicle) {

            if (!$this->showVehicle($licenceVehicle)) {
                continue;
            }

            $row = array_merge($licenceVehicle, $licenceVehicle['vehicle']);

            unset($row['vehicle']);
            unset($row['goodsDiscs']);

            $row['discNo'] = $this->getCurrentDiscNo($licenceVehicle);

            $results[] = $row;
        }

        return $results;
    }

    /**
     * Get current disc number
     *
     * @param array $licenceVehicle
     * @return string
     */
    protected function getCurrentDiscNo($licenceVehicle)
    {
        if ($this->isDiscPending($licenceVehicle)) {
            return 'Pending';
        }

        if (isset($licenceVehicle['goodsDiscs']) && !empty($licenceVehicle['goodsDiscs'])) {
            $currentDisc = $licenceVehicle['goodsDiscs'][0];

            return $currentDisc['discNo'];
        }

        return '';
    }

    /**
     * Check if the disc is pending
     *
     * @param array $licenceVehicleData
     * @return boolean
     */
    protected function isDiscPending($licenceVehicleData)
    {
        if (empty($licenceVehicleData['specifiedDate']) && empty($licenceVehicleData['deletedDate'])) {
            return true;
        }

        if (isset($licenceVehicleData['goodsDiscs']) && !empty($licenceVehicleData['goodsDiscs'])) {
            $currentDisc = $licenceVehicleData['goodsDiscs'][0];

            if (empty($currentDisc['ceasedDate']) && empty($currentDisc['discNo'])) {

                return true;
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
        $licences = $this->getOthersLicencesFromVrm($data['data']['vrm'], $this->getLicenceId());

        if (!empty($licences)) {

            $confirm = new Checkbox('confirm-add', array('label' => 'vehicle-belongs-to-another-licence-confirmation'));

            $confirm->setMessages(array($this->getErrorMessageForVehicleBelongingToOtherLicences($licences)));

            $form->get('licence-vehicle')->add($confirm);

            return true;
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
        $licenceVehicles = $this->getServiceLocator()->get('Entity\Vehicle')->getLicencesForVrm($vrm);

        $licences = array();

        foreach ($licenceVehicles as $licenceVehicle) {
            if (isset($licenceVehicle['licence']['id'])
                && $licenceVehicle['licence']['id'] != $licenceId) {

                $licenceNumber = 'UNKNOWN';

                if (empty($licenceVehicle['licence']['licNo'])
                    && isset($licenceVehicle['licence']['applications'][0])
                ) {
                    $licenceNumber = 'APP-' . $licenceVehicle['licence']['applications'][0]['id'];
                } else {
                    $licenceNumber = $licenceVehicle['licence']['licNo'];
                }

                $licences[] = $licenceNumber;
            }
        }

        return $licences;
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
        $translator = $this->getServiceLocator()->get('Helper\Translation');

        $translationKey = 'vehicle-belongs-to-another-licence-message-' . $this->lva;

        // Internally we can add the licence numbers
        if ($this->location == 'internal') {

            if (count($licences) > 1) {
                $translationKey .= '-multiple';
            }

            return sprintf($translator->translate($translationKey), implode(', ', $licences));
        }

        return $translator->translate($translationKey);
    }

    /**
     * Delete vehicles
     */
    protected function delete()
    {
        $licenceVehicleIds = explode(',', $this->params('child_id'));

        foreach ($licenceVehicleIds as $id) {

            $this->ceaseActiveDisc($id);

            $this->getServiceLocator()->get('Entity\LicenceVehicle')->delete($id);
        }
    }

    /**
     * If the latest disc is not active, cease it
     *
     * @param int $id
     */
    protected function ceaseActiveDisc($id)
    {
        $results = $this->getServiceLocator()->get('Entity\LicenceVehicle')->ceaseActiveDisc($id);

        if (!empty($results['goodsDiscs'])) {
            $activeDisc = $results['goodsDiscs'][0];

            if (empty($activeDisc['ceasedDate'])) {
                $activeDisc['ceasedDate'] = date('Y-m-d H:i:s');
                $this->getServiceLocator()->get('Entity\GoodsDisc')->save($activeDisc);
            }
        }
    }

    /**
     * Reprint action
     */
    public function reprintAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $this->reprintSave();

            return $this->redirect()->toRoute(
                null,
                array('id' => $this->params('id'))
            );
        }

        $form = $this->getConfirmationForm();

        return $this->render('reprint_vehicles', $form);
    }

    protected function getConfirmationForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('GenericConfirmation');
    }

    /**
     * Request a new disc
     *
     * @param array $data
     */
    protected function reprintSave()
    {
        $ids = explode(',', $this->params('child_id'));

        foreach ($ids as $id) {
            $this->reprintDisc($id);
        }
    }

    /**
     * Reprint a single disc
     *
     * @NOTE I have put this logic into its own method (rather in the reprintSave method), as we will soon be able to
     * reprint multiple discs at once
     *
     * @param int $id
     */
    protected function reprintDisc($id)
    {
        $this->ceaseActiveDisc($id);

        $this->requestDisc($id, 'Y');
    }

    /**
     * Request disc
     *
     * @param int $licenceVehicleId
     */
    protected function requestDisc($licenceVehicleId, $isCopy = 'N')
    {
        $data = array('licenceVehicle' => $licenceVehicleId, 'isCopy' => $isCopy);

        $this->getServiceLocator()->get('Entity\GoodsDisc')->save($data);
    }
}
