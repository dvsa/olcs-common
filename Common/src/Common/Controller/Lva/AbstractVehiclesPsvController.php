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
    private $type;

    private $typeMap = [
        'small'  => RefData::PSV_TYPE_SMALL,
        'medium' => RefData::PSV_TYPE_MEDIUM,
        'large'  => RefData::PSV_TYPE_LARGE
    ];

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

        $formHelper = $this->getServiceLocator()->get('Helper\Form');

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

            $this->maybeWarnAboutTotalAuth($resultData, false);

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

        $this->maybeWarnAboutTotalAuth($resultData);

        return $this->renderForm($form, 'vehicles_psv');
    }

    /**
     * Add a small vehicle
     */
    public function smallAddAction()
    {
        return $this->add('small');
    }

    /**
     * Edit a small vehicle
     */
    public function smallEditAction()
    {
        return $this->edit('small');
    }

    /**
     * Delete a small vehicle
     */
    public function smallDeleteAction()
    {
        $this->type = 'small';

        return $this->deleteAction();
    }

    /**
     * Transfer vehicles
     */
    public function smallTransferAction()
    {
        return $this->transferVehicles();
    }

    /**
     * Add a medium vehicle
     */
    public function mediumAddAction()
    {
        return $this->add('medium');
    }

    /**
     * Edit a medium vehicle
     */
    public function mediumEditAction()
    {
        return $this->edit('medium');
    }

    /**
     * Delete a medium vehicle
     */
    public function mediumDeleteAction()
    {
        $this->type = 'medium';

        return $this->deleteAction();
    }

    /**
     * Transfer vehicles
     */
    public function mediumTransferAction()
    {
        return $this->transferVehicles();
    }

    /**
     * Add a large vehicle
     */
    public function largeAddAction()
    {
        return $this->add('large');
    }

    /**
     * Edit a large vehicle
     */
    public function largeEditAction()
    {
        return $this->edit('large');
    }

    /**
     * Delete a large vehicle
     */
    public function largeDeleteAction()
    {
        $this->type = 'large';

        return $this->deleteAction();
    }

    /**
     * Transfer vehicles
     */
    public function largeTransferAction()
    {
        return $this->transferVehicles();
    }

    /**
     * Get the delete message.
     *
     * @return string
     */
    public function getDeleteMessage()
    {
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

    private function add($type)
    {
        $this->type = $type;
        $request = $this->getRequest();

        $resultData = $this->fetchResultData();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = [];
        }

        $params = [
            'mode' => 'add',
            'canAddAnother' => $resultData['available' . ucfirst($type) . 'Spaces'] > 1,
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
            $dtoData['type'] = $type;

            $response = $this->handleCommand($dtoClass::create($dtoData));

            if ($response->isOk()) {
                return $this->handlePostSave($type, false);
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

    private function edit($type)
    {
        $this->type = $type;
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

        if ($request->isPost() && $form->isValid()) {

            $formData = $form->getData();

            $dtoData = PsvVehiclesVehicle::mapFromForm($formData);
            $dtoData['id'] = $id;
            $dtoData[$this->getIdentifierIndex()] = $this->getIdentifier();

            $response = $this->handleCommand(CommandDto\LicenceVehicle\UpdatePsvLicenceVehicle::create($dtoData));

            if ($response->isOk()) {
                return $this->handlePostSave($type, false);
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
        if ($this->lva === 'licence' && $this->location === 'external' && $action === 'export') {
            $type = $this->getType();

            $resultData = $this->fetchResultData();

            return $this->getServiceLocator()
                ->get('Helper\Response')
                ->tableToCsv($this->getResponse(), $this->getTableBasic($type, $resultData), $type . '-vehicles');
        }

        if ($action === 'add') {
            $type = $this->getType();
            $resultData = $this->fetchResultData();

            if ($resultData['available' . ucfirst($type) . 'Spaces'] < 1) {

                $this->getServiceLocator()->get('Helper\FlashMessenger')
                    ->addErrorMessage('more-vehicles-than-total-auth-error');

                return $this->reload();
            }
        }
    }

    private function alterTable($table)
    {
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

    private function getTypeMap()
    {
        return $this->typeMap;
    }

    private function getTableNames()
    {
        $map = $this->getTypeMap();

        return array_keys($map);
    }

    /**
     * Remove vehicle size tables based on OC data
     *
     * @param Form $form
     * @return Form
     */
    private function alterForm($form, $resultData, $removeActions)
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        foreach ($this->getTableNames() as $tableName) {

            if (!$resultData['show' . ucfirst($tableName) . 'Table']) {
                $form->remove($tableName);
                continue;
            }

            if ($removeActions) {
                $tableConfigName = $tableName . '-readonly';
            } else {
                $tableConfigName = $tableName;
            }

            $table = $this->getTable($tableConfigName, $resultData[$tableName]);

            $formHelper->populateFormTable($form->get($tableName), $table, $tableName);

            if (!$removeActions && !$resultData['canTransfer']) {
                $table->removeAction('transfer');
            }
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

        if ($resultData['showLargeTable']) {
            $message .= '-large';
        }

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
     * Helper so we can always work out what type of PSV we're
     */
    private function getType()
    {
        if (isset($this->type)) {
            return $this->type;
        }

        $data = (array)$this->getRequest()->getPost();

        foreach ($this->getTableNames() as $type) {
            if (isset($data[$type]['action'])) {
                return $type;
            }
        }
    }

    /**
     * Get the total number of vehicles
     *
     * @return int
     */
    private function getTotalNumberOfVehicles($resultData)
    {
        $type = $this->getType();

        return count($resultData[$type]);
    }

    private function maybeWarnAboutTotalAuth($resultData, $current = true)
    {
        if ($this->lva !== 'licence' && $resultData['hasEnteredReg'] === 'Y') {

            $method = $current ? 'addCurrentWarningMessage' : 'addWarningMessage';

            foreach ($this->getTableNames() as $tableName) {
                if ($resultData[$tableName . 'AuthExceeded']) {
                    $this->getServiceLocator()->get('Helper\FlashMessenger')->$method(
                        'more-vehicles-than-' . $tableName . '-authorisation'
                    );
                }
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

    private function getTable($tableName, $tableData)
    {
        return $this->alterTable($this->getTableBasic($tableName, $tableData));
    }

    private function getTableBasic($tableName, $tableData)
    {
        return $this->getServiceLocator()
            ->get('Table')
            ->prepareTable('lva-psv-vehicles-' . $tableName, $tableData);
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

        foreach ($this->getTableNames() as $section) {

            if (isset($data[$section]['action'])) {

                $action = $this->getActionFromCrudAction($data[$section]);

                $data[$section]['routeAction'] = $section . '-' . strtolower($action);

                return $data[$section];
            }
        }

        return null;
    }

    private function fetchResultData()
    {
        $dtoClass = $this->queryMap[$this->lva];
        $dtoData = $this->getFilters();
        $dtoData['id'] = $this->getIdentifier();
        $response = $this->handleQuery($dtoClass::create($dtoData));
        return $response->getResult();
    }

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
