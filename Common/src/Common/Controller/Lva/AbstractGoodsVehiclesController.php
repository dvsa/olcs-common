<?php

/**
 * Goods Vehicles
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Controller\Lva\Traits\TransferVehiclesTrait;
use Common\Data\Mapper\Lva\GoodsVehicles;
use Common\Data\Mapper\Lva\GoodsVehiclesVehicle;
use Common\RefData;
use Dvsa\Olcs\Transfer\Command\Application\CreateGoodsVehicle as ApplicationCreateGoodsVehicle;
use Dvsa\Olcs\Transfer\Command\Licence\CreateGoodsVehicle as LicenceCreateGoodsVehicle;
use Dvsa\Olcs\Transfer\Command\Application\UpdateGoodsVehicle as ApplicationUpdateGoodsVehicle;
use Dvsa\Olcs\Transfer\Command\Licence\TransferVehicles;
use Dvsa\Olcs\Transfer\Command\Vehicle\ReprintDisc;
use Dvsa\Olcs\Transfer\Command\Vehicle\UpdateGoodsVehicle as LicenceUpdateGoodsVehicle;
use Dvsa\Olcs\Transfer\Command\Application\DeleteGoodsVehicle as ApplicationDeleteGoodsVehicle;
use Dvsa\Olcs\Transfer\Command\Vehicle\DeleteLicenceVehicle as LicenceDeleteLicenceVehicle;
use Dvsa\Olcs\Transfer\Command\Application\UpdateVehicles;
use Dvsa\Olcs\Transfer\Query\Licence\OtherActiveLicences;
use Dvsa\Olcs\Transfer\Query\LicenceVehicle\LicenceVehicle;
use Zend\Form\Element\Checkbox;
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
    use Traits\CrudTableTrait,
        TransferVehiclesTrait;

    protected $section = 'vehicles';
    protected $totalAuthorisedVehicles = [];
    protected $totalVehicles = [];

    protected $loadDataMap = [
        'licence' => LicenceGoodsVehicles::class,
        'variation' => VariationGoodsVehicles::class,
        'application' => ApplicationGoodsVehicles::class
    ];

    protected $createVehicleMap = [
        'licence' => LicenceCreateGoodsVehicle::class,
        'variation' => ApplicationCreateGoodsVehicle::class,
        'application' => ApplicationCreateGoodsVehicle::class
    ];

    protected $updateVehicleMap = [
        'licence' => LicenceUpdateGoodsVehicle::class,
        'variation' => ApplicationUpdateGoodsVehicle::class,
        'application' => ApplicationUpdateGoodsVehicle::class
    ];

    protected $deleteVehicleMap = [
        'licence' => LicenceDeleteLicenceVehicle::class,
        'variation' => ApplicationDeleteGoodsVehicle::class,
        'application' => ApplicationDeleteGoodsVehicle::class
    ];

    protected $headerData = null;

    protected function checkForAlternativeCrudAction($action)
    {
        return null;
    }

    public function indexAction()
    {
        $request = $this->getRequest();

        $headerData = $this->getHeaderData();

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

        $form = $this->getForm($headerData, $formData);

        if ($request->isPost() && $form->isValid()) {

            if ($this->lva === 'application') {

                $data = $form->getData()['data'];

                $dtoData = [
                    'id' => $this->getIdentifier(),
                    'version' => $data['version'],
                    'hasEnteredReg' => $data['hasEnteredReg'],
                    'partial' => $haveCrudAction
                ];

                $response = $this->handleCommand(UpdateVehicles::create($dtoData));

                if ($response->isServerError()) {
                    $this->getServiceLocator()->get('Helper\FlashMessenger')->addCurrentErrorMessage('unknown-error');
                    return $this->renderForm($form, $headerData);
                }

                if ($response->isClientError()) {
                    $this->mapErrors($form, $response->getResult()['messages']);
                    return $this->renderForm($form, $headerData);
                }
            }

            if ($haveCrudAction) {

                $alternativeCrudResponse = $this->checkForAlternativeCrudAction(
                    $this->getActionFromCrudAction($crudAction)
                );

                if ($alternativeCrudResponse !== null) {
                    return $alternativeCrudResponse;
                }

                return $this->handleCrudAction($crudAction, ['add', 'print-vehicles']);
            }

            return $this->completeSection('vehicles');
        }

        return $this->renderForm($form, $headerData);
    }

    protected function getHeaderData()
    {
        if ($this->headerData === null) {
            $dtoData = $this->getFilters();
            $dtoData['id'] = $this->getIdentifier();

            $dtoClass = $this->loadDataMap[$this->lva];

            $response = $this->handleQuery($dtoClass::create($dtoData));
            $this->headerData = $response->getResult();
        }

        return $this->headerData;
    }

    public function addAction()
    {
        $result = $this->getVehicleSectionData();

        if ($result['spacesRemaining'] < 1) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')
                ->addErrorMessage('more-vehicles-than-total-auth-error');

            return $this->redirect()->toRouteAjax(null, ['action' => null], [], true);
        }

        $request = $this->getRequest();
        $data = [];

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        }

        $params = [];
        $params['spacesRemaining'] = $result['spacesRemaining'];

        $form = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-goods-vehicles-add-vehicle')
            ->getForm($this->getRequest(), $params)
            ->setData($data);

        if ($request->isPost() && $form->isValid()) {

            $formData = $form->getData();

            $dtoData = [
                'id' => $this->getIdentifier(),
                'vrm' => $formData['data']['vrm'],
                'platedWeight' => $formData['data']['platedWeight'],
                'receivedDate' => isset($formData['licence-vehicle']['receivedDate'])
                    ? $formData['licence-vehicle']['receivedDate'] : null,
                'specifiedDate' => isset($formData['licence-vehicle']['specifiedDate'])
                    ? $formData['licence-vehicle']['specifiedDate'] : null,
                'confirm' => isset($data['licence-vehicle']['confirm-add'])
                    ? $data['licence-vehicle']['confirm-add'] : null
            ];

            $dtoClass = $this->createVehicleMap[$this->lva];
            $response = $this->handleCommand($dtoClass::create($dtoData));

            if ($response->isOk()) {
                return $this->handlePostSave(null, false);
            }

            if ($response->isServerError()) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addCurrentErrorMessage('unknown-error');
            } else {

                $messages = $response->getResult()['messages'];

                if (isset($messages['VE-VRM-2'])) {

                    $confirm = new Checkbox(
                        'confirm-add',
                        ['label' => 'vehicle-belongs-to-another-licence-confirmation']
                    );

                    $confirm->setMessages([$this->formatConfirmationMessage($messages['VE-VRM-2'])]);

                    $form->get('licence-vehicle')->add($confirm);

                } else {
                    $this->mapVehicleErrors($form, $messages);
                }
            }
        }

        return $this->render('add_vehicles', $form);
    }

    public function editAction()
    {
        $request = $this->getRequest();
        $id = $this->params('child_id');

        $response = $this->handleQuery(LicenceVehicle::create(['id' => $id]));

        $vehicleData = $response->getResult();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = GoodsVehiclesVehicle::mapFromResult($vehicleData);
        }

        $params = [
            'isRemoved' => !is_null($vehicleData['removalDate'])
        ];

        $form = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-goods-vehicles-edit-vehicle')
            ->getForm($this->getRequest(), $params)
            ->setData($data);

        if ($vehicleData['showHistory']) {
            $this->getServiceLocator()->get('Helper\Form')->populateFormTable(
                $form->get('vehicle-history-table'),
                $this->getServiceLocator()->get('Table')->prepareTable('lva-vehicles-history', $vehicleData['history'])
            );
        }

        if (!is_null($vehicleData['removalDate'])) {
            $this->getServiceLocator()->get('Helper\Form')
                ->disableValidation($form->getInputFilter());
        }

        // If the vehicle is removed, ignore validation
        if ($request->isPost() && $form->isValid()) {
            $formData = $form->getData();

            // Is removed
            if (!is_null($vehicleData['removalDate'])) {
                $dtoData = [
                    $this->getIdentifierIndex() => $this->getIdentifier(),
                    'id' => $id,
                    'version' => $formData['data']['version'],
                    'removalDate' => isset($formData['licence-vehicle']['removalDate'])
                        ? $formData['licence-vehicle']['removalDate'] : null,
                ];

                $dtoClass = $this->updateVehicleMap[$this->lva];

                $response = $this->handleCommand($dtoClass::create($dtoData));
            } else {
                $dtoData = [
                    $this->getIdentifierIndex() => $this->getIdentifier(),
                    'id' => $id,
                    'version' => $formData['data']['version'],
                    'platedWeight' => $formData['data']['platedWeight'],
                    'receivedDate' => isset($formData['licence-vehicle']['receivedDate'])
                        ? $formData['licence-vehicle']['receivedDate'] : null,
                    'specifiedDate' => isset($formData['licence-vehicle']['specifiedDate'])
                        ? $formData['licence-vehicle']['specifiedDate'] : null,
                ];

                $dtoClass = $this->updateVehicleMap[$this->lva];

                $response = $this->handleCommand($dtoClass::create($dtoData));
            }

            if ($response->isOk()) {
                return $this->handlePostSave(null, false);
            }

            if ($response->isServerError()) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addCurrentErrorMessage('unknown-error');
            } else {
                $this->mapVehicleErrors($form, $response->getResult()['messages']);
            }
        }

        return $this->render('edit_vehicles', $form);
    }

    protected function delete()
    {
        $ids = explode(',', $this->params('child_id'));

        $dtoData = [
            $this->getIdentifierIndex() => $this->getIdentifier(),
            'ids' => $ids
        ];

        $dtoClass = $this->deleteVehicleMap[$this->lva];

        $response = $this->handleCommand($dtoClass::create($dtoData));

        return $response->isOk();
    }

    /**
     * Get the delete message.
     *
     * @return string
     */
    public function getDeleteMessage()
    {
        $toDelete = count(explode(',', $this->params('child_id')));

        $result = $this->getVehicleSectionData();

        $acceptedLicenceTypes = [
            RefData::LICENCE_TYPE_STANDARD_NATIONAL,
            RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL
        ];

        if (!in_array($result['licenceType']['id'], $acceptedLicenceTypes)) {
            return 'delete.confirmation.text';
        }

        if ($result['activeVehicleCount'] > $toDelete) {
            return 'delete.confirmation.text';
        }

        return 'deleting.all.vehicles.message';
    }

    public function reprintAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $ids = explode(',', $this->params('child_id'));

            $response = $this->handleCommand(ReprintDisc::create(['ids' => $ids]));

            if (!$response->isOk()) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
            }

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
        return $this->transferVehicles();
    }

    protected function renderForm($form, $headerData)
    {
        if ($headerData['spacesRemaining'] < 0) {
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
                [
                    'label' => 'vehicle_table_action.reprint.label',
                    'requireRows' => true,
                ]
            );
        }

        if ($params['canTransfer']) {
            $table->addAction(
                'transfer',
                [
                    'label' => 'vehicle_table_action.transfer.label',
                    'class' => 'secondary js-require--multiple',
                    'requireRows' => true,
                ]
            );
        }

        if ($params['canExport']) {
            $table->addAction(
                'export',
                [
                    'label' => 'vehicle_table_action.export.label',
                    'requireRows' => true,
                    'class' => 'secondary js-disable-crud',
                ]
            );
        }

        if ($params['canPrintVehicle']) {
            $table->addAction(
                'print-vehicles',
                [
                    'label' => 'vehicle_table_action.print-vehicles.label',
                    'requireRows' => true,
                ]
            );
        }
    }

    protected function getConfirmationForm($request)
    {
        return $this->getServiceLocator()->get('Helper\Form')
            ->createFormWithRequest('GenericConfirmation', $request);
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

        if (isset($query['specified']) && in_array($query['specified'], ['Y', 'N'])) {
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
    protected function getForm($headerData, $formData)
    {
        return $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-goods-' . $this->section)
            ->getForm($this->getTable($headerData))
            ->setData($formData);
    }

    protected function mapErrors(\Zend\Form\Form $form, array $errors)
    {
        $formMessages = [];

        if (isset($errors['vehicles'])) {

            foreach ($errors['vehicles'] as $key => $message) {
                $formMessages['table']['table'][] = $key;
            }

            unset($errors['vehicles']);
        }

        $form->setMessages($formMessages);

        if (!empty($errors)) {
            $fm = $this->getServiceLocator()->get('Helper\FlashMessenger');

            foreach ($errors as $error) {
                $fm->addCurrentErrorMessage($error);
            }
        }
    }

    protected function mapVehicleErrors(\Zend\Form\Form $form, array $errors)
    {

        $formMessages = [];

        $form->setMessages($formMessages);

        if (!empty($errors)) {
            $fm = $this->getServiceLocator()->get('Helper\FlashMessenger');

            foreach ($errors as $error) {
                $fm->addCurrentErrorMessage($error);
            }
        }
    }

    /**
     * Format the confirmation message
     */
    protected function formatConfirmationMessage($message)
    {
        $translator = $this->getServiceLocator()->get('Helper\Translation');
        $decoded = json_decode($message);

        if (is_array($decoded)) {
            $message = 'vehicle-belongs-to-another-licence-message-internal';

            if (count($decoded) > 1) {
                $message .= '-multiple';
            }

            return $translator->translateReplace($message, [implode(', ', $decoded)]);
        } else {
            return $translator->translate('vehicle-belongs-to-another-licence-message-external');
        }
    }

    protected function postSave($section)
    {
        // @NOTE Prevents postSave from doing anything as this section has been migrated
        // @todo remove me once all sections have been migrated
    }

    protected function getVehicleSectionData()
    {
        $dtoData = [
            'id' => $this->getIdentifier(),
            'page' => 1,
            'limit' => 1
        ];
        $dtoClass = $this->loadDataMap[$this->lva];
        $response = $this->handleQuery($dtoClass::create($dtoData));

        return $response->getResult();
    }
}
