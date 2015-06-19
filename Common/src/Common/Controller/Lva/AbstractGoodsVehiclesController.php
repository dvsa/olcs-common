<?php

/**
 * Goods Vehicles
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Data\Mapper\Lva\GoodsVehicles;
use Common\Service\Table\Formatter\VehicleDiscNo;
use Zend\Form\Element\Checkbox;
use Common\Service\Entity\LicenceEntityService;
use Dvsa\Olcs\Transfer\Query\Licence\GoodsVehicles as LicenceGoodsVehicles;
use Dvsa\Olcs\Transfer\Query\Application\GoodsVehicles as ApplicationGoodsVehicles;
use Dvsa\Olcs\Transfer\Query\Variation\GoodsVehicles as VariationGoodsVehicles;

/**
 * Goods Vehicles
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractGoodsVehiclesController extends AbstractController
{
    use Traits\CrudTableTrait;

    protected $section = 'vehicles';
    protected $totalAuthorisedVehicles = [];
    protected $totalVehicles = [];

    protected $loadDataMap = [
        'licence' => LicenceGoodsVehicles::class,
        'variation' => VariationGoodsVehicles::class,
        'application' => ApplicationGoodsVehicles::class
    ];

    public function indexAction()
    {
        $request = $this->getRequest();

        $dtoData = $this->getFilters();
        $dtoData['id'] = $this->getIdentifier();

        $dtoClass = $this->loadDataMap[$this->lva];
        $response = $this->handleQuery($dtoClass::create($dtoData));
        $headerData = $response->getResult();

        $formData = [];
        $haveCrudAction = false;

        if ($request->isPost()) {
            $formData = (array)$request->getPost();
            $crudAction = $this->getCrudAction([$formData['table']]);
            $haveCrudAction = $crudAction !== null;
        } elseif ($this->lva === 'application') {
            $formData = GoodsVehicles::mapFromResult($headerData);
        }

        $formData = array_merge($formData, ['query' => (array)$request->getQuery()]);

        $form = $this->getForm($headerData, $formData, $haveCrudAction);

        if ($request->isPost() && $form->isValid()) {

            // @todo migrate this
            $response = $this->getServiceLocator()->get('BusinessServiceManager')
                ->get('Lva\\' . ucfirst($this->lva) . 'GoodsVehicles')
                ->process(
                    [
                        'id' => $this->getIdentifier(),
                        'data' => $form->getData()
                    ]
                );

            if (!$response->isOk()) {

                $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage($response->getMessage());
                return $this->renderForm($form);
            }

            if ($haveCrudAction) {

                $action = $this->getActionFromCrudAction($crudAction);

                $alternativeCrudAction = $this->checkForAlternativeCrudAction($action);

                if ($alternativeCrudAction === null) {
                    return $this->handleCrudAction($crudAction, array('add', 'print-vehicles'));
                }

                return $alternativeCrudAction;
            }

            return $this->completeSection('vehicles');
        }

        return $this->renderForm($form);
    }

    protected function getFilters()
    {
        if ($this->getRequest()->isPost()) {
            $query = $this->getRequest()->getPost('query');
        } else {
            $query = $this->getRequest()->getQuery();
        }

        return $this->formatFilters((array)$query);
    }

    protected function formatFilters($query)
    {
        $filters = [
            'page' => isset($query['page']) ? $query['page'] : 1,
            'limit' => isset($query['limit']) ? $query['limit'] : 10,
        ];

        if (isset($query['vrm']) && $query['vrm'] !== 'All') {
            $filters['vrm'] = $query['vrm'];
        }

        if (isset($query['specified'])) {
            $filters['specified'] = $query['specified'];
        }

        $filters['includeRemoved'] = (isset($query['includeRemoved']) && $query['includeRemoved'] == '1');

        if (isset($query['disc']) && in_array($query['disc'], ['Y', 'N'])) {
            $filters['disc'] = $query['disc'];
        }

        return $filters;
    }

    /**
     * Get pre-configured form
     *
     * @return \Zend\Form\Form
     */
    protected function getForm($headerData, $formData, $haveCrudAction)
    {
        return $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-goods-' . $this->section)
            ->getForm($this->getTable($headerData), $haveCrudAction)
            ->setData($formData);
    }

    public function addAction()
    {
        return $this->addOrEdit('add');
    }

    public function editAction()
    {
        return $this->addOrEdit('edit');
    }

    public function reprintAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $ids = explode(',', $this->params('child_id'));

            $this->getServiceLocator()->get('BusinessServiceManager')
                ->get('Lva\\ReprintDisc')
                ->process(
                    [
                        'ids' => $ids
                    ]
                );

            return $this->redirect()->toRouteAjax(
                null,
                array($this->getIdentifierIndex() => $this->getIdentifier())
            );
        }

        $form = $this->getConfirmationForm($request);

        return $this->render('reprint_vehicles', $form);
    }

    /**
     * Transfer vehicles action
     */
    public function transferAction()
    {
        $form = $this->getVehicleTransferForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData((array) $request->getPost());
            if ($form->isValid()) {
                $response = $this->getServiceLocator()->get('BusinessServiceManager')
                    ->get('Lva\TransferVehicles')
                    ->process(
                        [
                            'data' => $form->getData(),
                            'sourceLicenceId' => $this->getLicenceId(),
                            'targetLicenceId' => $form->get('data')->get('licence')->getValue(),
                            'id' => $this->params()->fromRoute('child_id')
                        ]
                    );

                if ($response->isOk()) {
                    $this->getServiceLocator()
                        ->get('Helper\FlashMessenger')
                        ->addSuccessMessage('licence.vehicles_transfer.form.vehicles_transfered');
                    return $this->redirect()->toRouteAjax(
                        null,
                        array($this->getIdentifierIndex() => $this->getIdentifier())
                    );
                }
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage($response->getMessage());
            }
        }
        return $this->renderForm($form);
    }

    /**
     * Hijack the crud action check so we can validate the add button
     *
     * @param string $action
     */
    protected function checkForAlternativeCrudAction($action)
    {
        if ($action === 'reprint') {
            $post = (array)$this->getRequest()->getPost();

            $id = $post['table']['id'];

            if ($this->isDiscPendingForLicenceVehicle($id)) {

                $this->getServiceLocator()->get('Helper\FlashMessenger')
                    ->addErrorMessage('reprint-pending-disc-error');

                return $this->reload();
            }
        }

        if ($action === 'add') {
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

            if (VehicleDiscNo::isDiscPending($results)) {
                return true;
            }
        }

        return false;
    }

    protected function canAddAnother()
    {
        $totalAuth = $this->getTotalNumberOfAuthorisedVehicles();
        $totalVehicles = $this->getTotalNumberOfVehicles();

        return $totalVehicles < ($totalAuth - 1);
    }

    /**
     * Get vrms linked to licence
     *
     * @return array
     * @todo migrate this
     */
    protected function getVrmsForCurrentLicence()
    {
        return $this->getServiceLocator()->get('Entity\Licence')->getCurrentVrms($this->getLicenceId());
    }

    /**
     * If vehicle exists on another licence, add a message and confirmation field to the form
     *
     * @param array $data
     * @param \Zend\Form\Form $form
     * @return boolean
     * @todo migrate this
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
     * @todo migrate this
     */
    protected function getOthersLicencesFromVrm($vrm, $licenceId)
    {
        $licenceVehicles = $this->getServiceLocator()->get('Entity\Vehicle')->getLicencesForVrm($vrm);

        $licences = array();

        foreach ($licenceVehicles as $licenceVehicle) {
            if (isset($licenceVehicle['licence']['id'])
                && $licenceVehicle['licence']['id'] != $licenceId) {

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
     * @todo migrate this
     */
    protected function getErrorMessageForVehicleBelongingToOtherLicences($licences)
    {
        $translator = $this->getServiceLocator()->get('Helper\Translation');

        $translationKey = 'vehicle-belongs-to-another-licence-message-' . $this->location;

        // Internally we can add the licence numbers
        if ($this->location === 'internal') {

            if (count($licences) > 1) {
                $translationKey .= '-multiple';
            }

            return sprintf($translator->translate($translationKey), implode(', ', $licences));
        }

        return $translator->translate($translationKey);
    }

    /**
     * Set appropriate default values on vehicle date fields
     *
     * @param \Zend\Form\Form $form
     * @param \DateTime $currentDate
     * @return \Zend\Form\Form
     */
    protected function setDefaultDates($form, $currentDate)
    {
        $fieldset = $form->get('licence-vehicle');

        // receivedDate gets removed in some contexts
        if ($fieldset->has('receivedDate')) {
            // default 'Received date' to the current date if it is not set
            $receivedDate = $fieldset->get('receivedDate')->getValue();
            $receivedDate = trim($receivedDate, '-'); // date element returns '--' when empty!
            if (empty($receivedDate)) {
                $fieldset->get('receivedDate')->setValue($currentDate);
            }
        }
        return $form;
    }

    /**
     * Get the delete message.
     *
     * @return string
     * @todo migrate this
     */
    public function getDeleteMessage()
    {
        $toDelete = count(explode(',', $this->params('child_id')));
        $total = $this->getTotalNumberOfVehicles();

        $licence = $this->getServiceLocator()->get('Entity\Licence')->getOverview($this->getLicenceId());

        $acceptedLicenceTypes = array(
            LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
            LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
        );

        if (!in_array($licence['licenceType']['id'], $acceptedLicenceTypes)) {
            return 'delete.confirmation.text';
        }

        if ($total !== $toDelete) {
            return 'delete.confirmation.text';
        }

        return 'deleting.all.vehicles.message';
    }

    /**
     * Get vehicles transfer form
     *
     * @return \Zend\Form\Form
     * @todo migrate this
     */
    protected function getVehicleTransferForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $form = $formHelper->createForm('Lva\VehiclesTransfer');
        $licences = $this->getServiceLocator()
            ->get('Entity\Licence')
            ->getOtherActiveLicences($this->params()->fromRoute('licence'));
        $form->get('data')->get('licence')->setValueOptions($licences);
        $formHelper->setFormActionFromRequest($form, $this->getRequest());
        return $form;
    }

    /**
     * @todo migrate this
     */
    protected function delete()
    {
        $ids = explode(',', $this->params('child_id'));

        $this->getServiceLocator()->get('BusinessServiceManager')
            ->get('Lva\\DeleteGoodsVehicle')
            ->process(
                [
                    'ids' => $ids
                ]
            );
    }

    protected function renderForm($form)
    {
        // *always* check if the user has exceeded their authority
        // as a nice little addition; they may have changed their OC totals

        // @todo migrate this
        if ($this->getTotalNumberOfVehicles() > $this->getTotalNumberOfAuthorisedVehicles()) {
            $this->getServiceLocator()->get('Helper\Guidance')->append('more-vehicles-than-authorisation');
        }

        $files = ['lva-crud', 'vehicle-goods'];
        $params = [];

        $filterForm = $this->getServiceLocator()->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-goods-' . $this->section . '-filters')
            ->getForm();

        if ($filterForm !== null) {
            $files[] = 'forms/filter';
            $params['filterForm'] = $filterForm;

            $query = (array)$this->getRequest()->getQuery();

            if (!isset($query['limit']) || !is_numeric($query['limit'])) {
                $query['limit'] = 10;
            }

            $filterForm->setData($query);
        }

        $this->getServiceLocator()->get('Script')->loadFiles($files);

        return $this->render('vehicles', $form, $params);
    }

    /**
     * Common add / edit logic
     */
    protected function addOrEdit($mode)
    {
        $request = $this->getRequest();
        $id = $this->params('child_id');

        $editRemovedVehicleForLicence = false;

        // Check if the user is attempting to edit a removed vehicle, and bail early if so
        if ($mode === 'edit' && $request->isPost()) {

            $vehicleData = $this->getVehicleFormData($id);

            if ($this->lva === 'licence' && $this->location === 'internal'
                && isset($vehicleData['removalDate']) && !empty($vehicleData['removalDate'])
            ) {
                $editRemovedVehicleForLicence = true;
            } elseif (isset($vehicleData['removalDate']) && !empty($vehicleData['removalDate'])) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')
                    ->addErrorMessage('cant-edit-removed-vehicle');

                return $this->redirect()->toRoute(null, [], [], true);
            }
        }

        $data = array();

        if ($request->isPost() && !$editRemovedVehicleForLicence) {
            $data = (array)$request->getPost();
        } elseif ($mode === 'edit') {
            $vehicleData = $this->getVehicleFormData($id);
            $data = $this->formatVehicleDataForForm($vehicleData);

            // if we are in edit removed vehicle mode - we need to merge
            // fetched data with some POST data, because almost all fields are
            // disabled so there is no data in POST
            if ($request->isPost() && $editRemovedVehicleForLicence) {
                $post = (array)$request->getPost();
                $data['licence-vehicle']['removalDate'] = $post['licence-vehicle']['removalDate'];
                $data['security'] = $post['security'];
            }
        }

        $params = [
            'mode' => $mode,
            'isRemoved' => isset($vehicleData['removalDate']) && !empty($vehicleData['removalDate']),
            'id' => $id,
            'canAddAnother' => $this->canAddAnother(),
            'isPost' => $request->isPost(),
            'currentVrms' => $this->getVrmsForCurrentLicence(),
            'lva' => $this->lva
        ];

        $form = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-goods-' . $this->section . '-vehicle')
            ->getForm($this->getRequest(), $params)
            ->setData($data);

        if ($request->isPost() && $form->isValid()) {

            // We can save if we are in
            // - edit mode
            // - add mode, and we have confirmed add
            // - add mode, and haven't confirmed add, but the VRM is new
            if ($mode === 'edit'
                || (isset($data['licence-vehicle']['confirm-add']) && !empty($data['licence-vehicle']['confirm-add']))
                || !$this->checkIfVehicleExistsOnOtherLicences($data, $form)
            ) {

                $params = [
                    'data' => $form->getData(),
                    'mode' => $mode,
                    'id' => $this->getIdentifier(),
                    'licenceId' => $this->getLicenceId()
                ];

                $response = $this->getServiceLocator()->get('BusinessServiceManager')
                    ->get('Lva\\' . ucfirst($this->lva) . 'GoodsVehiclesVehicle')
                    ->process($params);

                if (!$response->isOk()) {
                    $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage($response->getMessage());
                    return $this->renderForm($form);
                }

                return $this->handlePostSave();
            }
        }

        if ($this->location === 'internal') {
            // set default date values prior to render
            $today = $this->getServiceLocator()->get('Helper\Date')->getDateObject();
            $form = $this->setDefaultDates($form, $today);
        }

        return $this->render($mode . '_vehicles', $form);
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

        $licenceVehicle['discNo'] = VehicleDiscNo::format($licenceVehicle);
        unset($licenceVehicle['goodsDiscs']);

        return array(
            'licence-vehicle' => $licenceVehicle,
            'data' => $data['vehicle']
        );
    }

    protected function getTable($headerData)
    {
        $query = $this->getRequest()->getQuery();
        $params = array_merge((array)$query, ['query' => $query]);

        $tableName = 'lva-' . $this->location . '-vehicles';

        $table = $this->getServiceLocator()->get('Table')
            ->prepareTable($tableName, $headerData['licenceVehicles'], $params);

        $this->makeTableAlterations($table, $headerData);

        return $table;
    }

    protected function makeTableAlterations($table, $params)
    {
        if ($params['canReprint']) {
            $table->addAction(
                'reprint',
                ['label' => 'Reprint Disc', 'requireRows' => true]
            );
        }

        if ($params['canTransfer']) {
            $table->addAction(
                'transfer',
                ['label' => 'Transfer', 'class' => 'secondary js-require--multiple', 'requireRows' => true]
            );
        }

        if ($params['canExport']) {
            $table->addAction(
                'export',
                ['requireRows' => true, 'class' => 'secondary js-disable-crud']
            );
        }

        if ($params['canPrintVehicle']) {
            $table->addAction(
                'print-vehicles',
                ['label' => 'Print vehicle list', 'requireRows' => true]
            );
        }
    }

    protected function getConfirmationForm($request)
    {
        return $this->getServiceLocator()->get('Helper\Form')
            ->createFormWithRequest('GenericConfirmation', $request);
    }

    protected function getVehicleFormData($id)
    {
        return $this->getServiceLocator()->get('Entity\LicenceVehicle')->getVehicle($id);
    }

    /**
     * Get the total vehicle authorisations
     *
     * @return int
     */
    protected function getTotalNumberOfAuthorisedVehicles()
    {
        if (empty($this->totalAuthorisedVehicles)) {
            $this->totalAuthorisedVehicles = $this->getLvaEntityService()
                ->getTotalVehicleAuthorisation($this->getIdentifier());
        }

        return $this->totalAuthorisedVehicles;
    }

    /**
     * Get total number of vehicles
     *
     * @return int
     */
    protected function getTotalNumberOfVehicles()
    {
        if (empty($this->totalVehicles)) {
            $this->totalVehicles = $this->getServiceLocator()->get('Entity\Licence')
                ->getVehiclesTotal($this->getLicenceId());
        }

        return $this->totalVehicles;
    }
}
