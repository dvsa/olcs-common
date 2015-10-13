<?php

/**
 * Vehicles PSV Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Controller\Lva\Traits\TransferVehiclesTrait;
use Common\Data\Mapper\Lva\PsvVehicles;
use Common\Data\Mapper\Lva\PsvVehiclesVehicle;
use Common\RefData;
use Common\Service\Entity\LicenceEntityService;
use Dvsa\Olcs\Transfer\Command\Application\UpdatePsvVehicles;
use Zend\Form\Form;
use Zend\Form\Element\Checkbox;
use Dvsa\Olcs\Transfer\Query as QueryDto;
use Dvsa\Olcs\Transfer\Command as CommandDto;
use Dvsa\Olcs\Transfer\Command\Application\CreatePsvVehicle as ApplicationCreatePsvVehicle;
use Dvsa\Olcs\Transfer\Command\Licence\CreatePsvVehicle as LicenceCreatePsvVehicle;

/**
 * Vehicles PSV Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractVehiclesPsvController extends AbstractController
{
    use Traits\CrudTableTrait,
        TransferVehiclesTrait;

    protected $section = 'vehicles_psv';

    private $queryMap = [
        'licence' => QueryDto\Licence\PsvVehicles::class,
        'variation' => QueryDto\Variation\PsvVehicles::class,
        'application' => QueryDto\Application\PsvVehicles::class
    ];

    private $deleteMap = [
        'licence' => CommandDto\Vehicle\DeleteLicenceVehicle::class,
        'variation' => CommandDto\Application\DeletePsvVehicle::class,
        'application' => CommandDto\Application\DeletePsvVehicle::class
    ];

    protected $createVehicleMap = [
        'licence' => LicenceCreatePsvVehicle::class,
        'variation' => ApplicationCreatePsvVehicle::class,
        'application' => ApplicationCreatePsvVehicle::class
    ];

    public function indexAction()
    {
        $request = $this->getRequest();

        $resultData = $this->fetchResultData();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = PsvVehicles::mapFromResult($resultData);
        }

        $form = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-' . $this->section)
            ->getForm()
            ->setData($data);

        $removeActions = false;
        if (!$resultData['hasBreakdown'] && in_array($this->lva, ['licence', 'variation'])) {
            $removeActions = true;
            $this->addGuidance($resultData);
        }

        $form = $this->alterForm($form, $resultData, $removeActions);

        if ($request->isPost() && $form->isValid()) {

            $crudAction = $this->getCrudAction($data);

            $response = $this->updateVehiclesSection($form, $crudAction);

            if ($response !== null) {
                return $response;
            }

            if ($crudAction !== null) {
                $alternativeCrudResponse = $this->checkForAlternativeCrudAction($crudAction);

                if ($alternativeCrudResponse !== null) {
                    return $alternativeCrudResponse;
                }

                // handle the original action as planned
                return $this->handleCrudAction($data['vehicles']);
            }

            return $this->completeSection('vehicles_psv');
        }

        $this->maybeWarnAboutTotalAuth($resultData);

        return $this->renderForm($form, 'vehicles_psv');
    }

    protected function updateVehiclesSection($form, $crudAction)
    {
        if ($this->lva === 'application') {
            $formData = $form->getData();

            $dtoData = [
                'id' => $this->getIdentifier(),
                'version' => $formData['data']['version'],
                'hasEnteredReg' => $formData['data']['hasEnteredReg'],
                'partial' => $crudAction !== null
            ];

            $resultData['hasEnteredReg'] = $formData['data']['hasEnteredReg'];

            $response = $this->handleCommand(UpdatePsvVehicles::create($dtoData));

            if ($response->isClientError()) {
                PsvVehicles::mapFormErrors(
                    $form,
                    $response->getResult()['messages'],
                    $this->getServiceLocator()->get('Helper\FlashMessenger')
                );
                return $this->renderForm($form, 'vehicles_psv');
            }

            if ($response->isServerError()) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addUnknownError();
                return $this->renderForm($form, 'vehicles_psv');
            }
        }

        if ($this->lva === 'licence' && $this->location === 'external') {

            $shareInfo = $form->getData()['shareInfo']['shareInfo'];

            $dtoData = [
                'id' => $this->getIdentifier(),
                'shareInfo' => $shareInfo
            ];

            $response = $this->handleCommand(CommandDto\Licence\UpdateVehicles::create($dtoData));

            if (!$response->isOk()) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addCurrentErrorMessage('unknown-error');
                return $this->renderForm($form, 'vehicles_psv');
            }
        }
    }

    /**
     * Get the delete message.
     *
     * @return string
     */
    public function getDeleteMessage()
    {
        if ($this->lva === 'application') {
            return 'delete.confirmation.text';
        }

        $resultData = $this->fetchResultData();

        $toDelete = count(explode(',', $this->params('child_id')));
        $total = $this->getTotalNumberOfVehicles($resultData);

        $acceptedLicenceTypes = [
            LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
            LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
        ];

        if (in_array($resultData['licenceType']['id'], $acceptedLicenceTypes) && $total == $toDelete) {
            return 'deleting.all.vehicles.message';
        }

        return 'delete.confirmation.text';
    }

    /**
     * Transfer vehicles
     */
    public function transferAction()
    {
        return $this->transferVehicles();
    }

    /**
     * Add a vehicle action
     *
     * @return type
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $resultData = $this->fetchResultData();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = [];
        }

        $params = [
            'mode' => 'add',
            'canAddAnother' => $resultData['availableSpaces'] > 1,
            'action' => $this->params('action'),
            'isRemoved' => false
        ];

        $form = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-' . $this->section . '-vehicle')
            ->getForm($this->getRequest(), $params)
            ->setData($data);

        if ($request->isPost() && $form->isValid()) {

            $formData = $form->getData();

            $dtoClass = $this->createVehicleMap[$this->lva];

            $dtoData = PsvVehiclesVehicle::mapFromForm($formData);
            $dtoData[$this->getIdentifierIndex()] = $this->getIdentifier();

            $response = $this->handleCommand($dtoClass::create($dtoData));

            if ($response->isOk()) {
                return $this->handlePostSave();
            }

            $fm = $this->getServiceLocator()->get('Helper\FlashMessenger');

            if ($response->isClientError()) {
                PsvVehiclesVehicle::mapFormErrors($form, $response->getResult()['messages'], $fm);
            }

            if ($response->isServerError()) {
                $fm->addCurrentUnknownError();
            }
        }

        return $this->render('add_vehicle', $form);
    }

    /**
     * Edit action
     *
     * @return type
     */
    public function editAction()
    {
        $request = $this->getRequest();

        $id = $this->params('child_id');
        $resultData = $this->fetchItemData($id);
        $data = PsvVehiclesVehicle::mapFromResult($resultData);

        if ($request->isPost()) {
            $postData = (array)$request->getPost();
            $data = $postData;
            $data['data'] = array_merge($data['data'], $postData['data']);
            if (isset($data['licence-vehicle']) && isset($postData['licence-vehicle'])) {
                $data['licence-vehicle'] = array_merge($data['licence-vehicle'], $postData['licence-vehicle']);
            }
        }

        $params = [
            'mode' => 'edit',
            'canAddAnother' => false,
            'action' => $this->params('action'),
            'isRemoved' => !empty($resultData['removalDate']),
            'location' => $this->location
        ];

        $form = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-' . $this->section . '-vehicle')
            ->getForm($this->getRequest(), $params)
            ->setData($data);

        if ($resultData['showHistory']) {
            $table = $this->getServiceLocator()->get('Table')
                ->prepareTable('lva-psv-vehicles-history', $resultData['history']);
            $table->removeColumn('discNo');
            $this->getServiceLocator()->get('Helper\Form')->populateFormTable(
                $form->get('vehicle-history-table'),
                $table
            );
        }

        if ($request->isPost() && $form->isValid()) {

            $formData = $form->getData();

            $dtoData = PsvVehiclesVehicle::mapFromForm($formData);
            $dtoData['id'] = $id;
            $dtoData[$this->getIdentifierIndex()] = $this->getIdentifier();

            $response = $this->handleCommand(CommandDto\LicenceVehicle\UpdatePsvLicenceVehicle::create($dtoData));

            if ($response->isOk()) {
                return $this->handlePostSave();
            }

            $fm = $this->getServiceLocator()->get('Helper\FlashMessenger');

            if ($response->isClientError()) {
                PsvVehiclesVehicle::mapFormErrors($form, $response->getResult()['messages'], $fm);
            }

            if ($response->isServerError()) {
                $fm->addCurrentUnknownError();
            }
        }

        return $this->render('edit_vehicle', $form);
    }

    /**
     * Hijack the crud action check so we can validate the add button
     *
     * @param string $action
     */
    protected function checkForAlternativeCrudAction($action)
    {
        // export to CSV action
        if ($this->lva === 'licence' && $this->location === 'external' && $action === 'export') {
            $resultData = $this->fetchResultData();
            $data = $resultData['vehicles'];

            return $this->getServiceLocator()
                ->get('Helper\Response')
                ->tableToCsv($this->getResponse(), $this->getTableBasic($data), 'psv-vehicles');
        }

        // if adding check that we haven't exceeded the to the totAuthVehicles
        if ($action === 'add') {
            $resultData = $this->fetchResultData();
            if ($resultData['availableSpaces'] < 1) {

                $this->getServiceLocator()->get('Helper\FlashMessenger')
                    ->addErrorMessage('more-vehicles-than-total-auth-error');

                return $this->reload();
            }
        }
    }

    private function alterTable($table)
    {
        // if licence on external then add an "Export" action
        if ($this->lva === 'licence' && $this->location === 'external') {
            $table->addAction(
                'export',
                [
                    'requireRows' => true,
                    'class' => 'secondary js-disable-crud'
                ]
            );
        }

        return $table;
    }

    /**
     * Remove vehicle size tables based on OC data
     *
     * @param Form $form
     * @return Form
     */
    private function alterForm(Form $form, $resultData, $removeActions)
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $table = $this->getTable($resultData['vehicles'], $removeActions);

        $formHelper->populateFormTable($form->get('vehicles'), $table, 'vehicles');

        if (!$removeActions && !$resultData['canTransfer']) {
            $table->removeAction('transfer');
        }

        if (in_array($this->lva, ['licence', 'variation'])) {
            $this->getServiceLocator()->get('FormServiceManager')
                ->get('lva-licence-variation-vehicles')->alterForm($form);
        }

        return $form;
    }

    private function addGuidance($resultData)
    {
        $message = 'psv-vehicles-' . $this->lva . '-missing-breakdown';

        if ($this->lva === 'variation') {
            $link = $this->url()->fromRoute('lva-variation/operating_centres', [], [], true);

            $class = '';
        } else {
            $params = ['licence' => $this->getIdentifier(), 'redirectRoute' => 'operating_centres'];
            $link = $this->getServiceLocator()->get('Helper\Url')->fromRoute('lva-licence/variation', $params);

            $class = 'js-modal-ajax';
        }

        $translator = $this->getServiceLocator()->get('Helper\Translation');
        $message = $translator->translateReplace($message, [$link, $class]);

        $this->getServiceLocator()->get('Helper\Guidance')->append($message);
    }

    /**
     * Get the total number of vehicles
     *
     * @return int
     */
    private function getTotalNumberOfVehicles($resultData)
    {
        return $resultData['total'];
    }

    /**
     * Add a Flash messenger warning if the total vehicles has been exceeded
     *
     * @param array $resultData
     * @param bool  $current
     */
    private function maybeWarnAboutTotalAuth($resultData, $current = true)
    {
        if ($this->lva !== 'licence' && $resultData['hasEnteredReg'] === 'Y') {

            $method = $current ? 'addCurrentWarningMessage' : 'addWarningMessage';
            if ((int) $resultData['availableSpaces'] < 0) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->$method(
                    'more-vehicles-than-authorisation'
                );
            }
        }
    }

    /**
     * Delete vehicles
     */
    protected function delete()
    {
        $licenceVehicleIds = explode(',', $this->params('child_id'));

        $dtoClass = $this->deleteMap[$this->lva];

        $dtoData = [
            'ids' => $licenceVehicleIds,
            $this->getIdentifierIndex() => $this->getIdentifier()
        ];

        $this->handleCommand($dtoClass::create($dtoData));
    }

    /**
     * Get the altered table
     *
     * @param array $tableData
     * @param bool  $readOnly
     *
     * @return \Common\Service\Table\TableBuilder
     */
    private function getTable($tableData, $readOnly = false)
    {
        return $this->alterTable($this->getTableBasic($tableData, $readOnly));
    }

    /**
     * Get the table
     *
     * @param array $tableData
     * @param bool  $readOnly
     *
     * @return \Common\Service\Table\TableBuilder
     */
    private function getTableBasic($tableData, $readOnly = false)
    {
        $tableName = 'lva-psv-vehicles';
        if ($readOnly) {
            $tableName .= '-readonly';
        }

        return $this->getServiceLocator()->get('Table')->prepareTable($tableName, $tableData);
    }

    private function renderForm($form)
    {
        $params = [];

        $files = ['lva-crud', 'vehicle-psv'];

        if (!($this->lva === 'application' && $this->location === 'external')) {
            $filterForm = $this->getServiceLocator()->get('FormServiceManager')
                ->get('lva-psv-vehicles-filters')
                ->getForm();

            if ($filterForm !== null) {
                $files[] = 'forms/filter';
                $params['filterForm'] = $filterForm;

                $query = (array)$this->getRequest()->getQuery();

                $filterForm->setData($query);
            }
        }
        $this->getServiceLocator()->get('Script')->loadFiles($files);
        return $this->render('vehicles_psv', $form, $params);
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

        if (isset($data['vehicles']['action'])) {
            $action = $this->getActionFromCrudAction($data['vehicles']);

            return $action;
        }

        return null;
    }

    /**
     * Fetch vehicle data
     *
     * @return array
     */
    private function fetchResultData()
    {
        $dtoClass = $this->queryMap[$this->lva];
        $dtoData = $this->getFilters();
        $dtoData['id'] = $this->getIdentifier();
        $response = $this->handleQuery($dtoClass::create($dtoData));
        return $response->getResult();
    }

    /**
     * Fetch one vehicles data
     *
     * @param int $id
     *
     * @return array
     */
    private function fetchItemData($id)
    {
        $response = $this->handleQuery(QueryDto\LicenceVehicle\PsvLicenceVehicle::create(['id' => $id]));

        return $response->getResult();
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
        $filters = [];
        $filters['includeRemoved'] = (isset($query['includeRemoved']) && $query['includeRemoved'] == '1');
        return $filters;
    }
}
