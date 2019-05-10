<?php

namespace Common\Controller\Lva;

use Common\Controller\Lva\Traits\TransferVehiclesTrait;
use Common\Controller\Lva\Traits\VehicleSearchTrait;
use Common\Data\Mapper;
use Common\RefData;
use Common\Service\Table\TableBuilder;
use Dvsa\Olcs\Transfer\Command\Application\CreateGoodsVehicle as ApplicationCreateGoodsVehicle;
use Dvsa\Olcs\Transfer\Command\Application\DeleteGoodsVehicle as ApplicationDeleteGoodsVehicle;
use Dvsa\Olcs\Transfer\Command\Application\UpdateGoodsVehicle as ApplicationUpdateGoodsVehicle;
use Dvsa\Olcs\Transfer\Command\Application\UpdateVehicles as AppUpdateVehicles;
use Dvsa\Olcs\Transfer\Command\Licence\CreateGoodsVehicle as LicenceCreateGoodsVehicle;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateVehicles as LicUpdateVehicles;
use Dvsa\Olcs\Transfer\Command\Vehicle\DeleteLicenceVehicle as LicenceDeleteLicenceVehicle;
use Dvsa\Olcs\Transfer\Command\Vehicle\ReprintDisc;
use Dvsa\Olcs\Transfer\Command\Vehicle\UpdateGoodsVehicle as LicenceUpdateGoodsVehicle;
use Dvsa\Olcs\Transfer\Query as TransferQry;
use Dvsa\Olcs\Transfer\Query\LicenceVehicle\LicenceVehicle;
use Zend\Form\Element\Checkbox;
use Zend\Form\FormInterface;

/**
 * Goods Vehicles
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractGoodsVehiclesController extends AbstractController
{
    const DEF_TABLE_FIRST_PAGE_NR = 1;
    const DEF_TABLE_ITEMS_COUNT = 25;

    const SEARCH_VEHICLES_COUNT = 20;

    use TransferVehiclesTrait,
        VehicleSearchTrait;
    use Traits\CrudTableTrait {
        handleCrudAction as protected traitHandleCrudAction;
    }

    protected $section = 'vehicles';
    protected $baseRoute = 'lva-%s/vehicles';

    protected $totalAuthorisedVehicles = [];
    protected $totalVehicles = [];

    protected $loadDataMap = [
        'licence' => TransferQry\Licence\GoodsVehicles::class,
        'variation' => TransferQry\Variation\GoodsVehicles::class,
        'application' => TransferQry\Application\GoodsVehicles::class,
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

    /**
     * Additional functionality for action
     *
     * @param string $action Action
     *
     * @return \Zend\Http\Response
     */
    protected function checkForAlternativeCrudAction($action)
    {
        return null;
    }

    /**
     * Process Index action
     *
     * @return \Common\View\Model\Section|null|\Zend\Http\Response
     */
    public function indexAction()
    {
        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();

        $headerData = $this->getHeaderData();
        if ($headerData === null) {
            return $this->notFoundAction();
        }

        $formData = [];

        if ($request->isPost()) {
            $formData = (array)$request->getPost();
        } elseif ($this->lva === 'application') {
            $formData = Mapper\Lva\GoodsVehicles::mapFromResult($headerData);
        } elseif ($this->lva === 'licence') {
            $formData = Mapper\Lva\LicenceGoodsVehicles::mapFromResult($headerData);
        }

        $formData = array_merge($formData, ['query' => (array)$request->getQuery()]);
        $form = $this->getForm($headerData, $formData);

        if ($request->isPost()) {
            $crudAction = $this->getCrudAction([$formData['table']]);
            $haveCrudAction = ($crudAction !== null);

            if ($haveCrudAction && $this->isInternalReadOnly()) {
                return $this->handleCrudAction($crudAction);
            }

            if ($form->isValid()) {
                $response = $this->updateVehiclesSection($form, $haveCrudAction, $headerData);
                if ($response !== null) {
                    return $response;
                }

                if ($haveCrudAction) {
                    return $this->handleCrudAction($crudAction);
                }

                return $this->completeSection('vehicles');
            }
        }

        return $this->renderForm($form, $headerData);
    }

    /**
     * Override handleCrudAction
     *
     * @param array $crudActionData Action data
     *
     * @return \Zend\Http\Response
     */
    protected function handleCrudAction(array $crudActionData)
    {
        $alternativeCrudResponse = $this->checkForAlternativeCrudAction(
            $this->getActionFromCrudAction($crudActionData)
        );
        if ($alternativeCrudResponse !== null) {
            return $alternativeCrudResponse;
        }

        return $this->traitHandleCrudAction(
            $crudActionData,
            [
                'add', 'print-vehicles', 'export', 'show-removed-vehicles', 'hide-removed-vehicles'
            ]
        );
    }

    /**
     * Update Vehicle Section
     *
     * @param \Common\Form\Form $form           Form
     * @param string            $haveCrudAction Action
     * @param array             $headerData     Data from db
     *
     * @return \Common\View\Model\Section|null
     */
    protected function updateVehiclesSection(FormInterface $form, $haveCrudAction, $headerData)
    {
        if ($this->lva === 'application') {
            $data = $form->getData()['data'];

            $dtoData = [
                'id' => $this->getIdentifier(),
                'version' => $data['version'],
                'hasEnteredReg' => $data['hasEnteredReg'],
                'partial' => $haveCrudAction
            ];

            $response = $this->handleCommand(AppUpdateVehicles::create($dtoData));

            if ($response->isServerError()) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addCurrentErrorMessage('unknown-error');
                return $this->renderForm($form, $headerData);
            }

            if ($response->isClientError()) {
                $this->mapErrors($form, $response->getResult()['messages']);
                return $this->renderForm($form, $headerData);
            }
        }

        if ($this->lva === 'licence') {
            $shareInfo = $form->getData()['shareInfo']['shareInfo'];

            $dtoData = [
                'id' => $this->getIdentifier(),
                'shareInfo' => $shareInfo
            ];

            $response = $this->handleCommand(LicUpdateVehicles::create($dtoData));

            if (!$response->isOk()) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addCurrentErrorMessage('unknown-error');
                return $this->renderForm($form, $headerData);
            }
        }

        return null;
    }

    /**
     * Request data from API
     *
     * @return array|null
     */
    protected function getHeaderData()
    {
        if ($this->headerData === null) {
            $dtoData = $this->getFilters();
            $dtoData['id'] = $this->getIdentifier();

            $dtoClass = $this->loadDataMap[$this->lva];

            /** @var \Common\Service\Cqrs\Response $response */
            $response = $this->handleQuery($dtoClass::create($dtoData));
            if ($response->isForbidden()) {
                return null;
            }

            $this->headerData = $response->getResult();
        }

        return $this->headerData;
    }

    /**
     * Process Add action
     *
     * @return \Common\View\Model\Section|\Zend\Http\Response
     */
    public function addAction()
    {
        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();

        $result = $this->getVehicleSectionData();

        if ($result['spacesRemaining'] < 1) {
            if ($this->lva === 'variation' || $this->lva === 'application') {
                $message = $this->getServiceLocator()->get('Helper\Translation')
                    ->translateReplace(
                        'markup-more-vehicles-than-total-auth-error-variation',
                        [
                            $result['totAuthVehicles'],
                            $this->url()->fromRoute(
                                'lva-' . $this->lva .
                                '/operating_centres',
                                ['action' => null],
                                [],
                                true
                            )
                        ]
                    );
            } else {
                $variation = $this->getServiceLocator()->get('Lva\Variation');

                $message = $this->getServiceLocator()->get('Helper\Translation')
                    ->translateReplace(
                        'markup-more-vehicles-than-total-auth-error',
                        [
                            $result['totAuthVehicles'],
                            $variation->getVariationLink($this->getLicenceId(), 'operating_centres')
                        ]
                    );
            }

            $this->getServiceLocator()->get('Helper\FlashMessenger')
                ->addProminentErrorMessage($message);

            return $this->redirect()->toRouteAjax(
                $this->getBaseRoute(),
                ['action' => null],
                ['query' => $request->getQuery()->toArray()],
                true
            );
        }

        $data = [];

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        }

        $params = [];
        $params['spacesRemaining'] = $result['spacesRemaining'];

        /** @var \Zend\Form\FormInterface $form */
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
                return $this->handlePostSave(null, ['query' => $request->getQuery()->toArray()]);
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

    /**
     * Process Edit Action
     *
     * @return \Common\View\Model\Section|\Zend\Http\Response
     */
    public function editAction()
    {
        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();
        $id = $this->params('child_id');

        $response = $this->handleQuery(LicenceVehicle::create(['id' => $id]));

        $vehicleData = $response->getResult();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = Mapper\Lva\GoodsVehiclesVehicle::mapFromResult($vehicleData);
        }

        $params = [
            'isRemoved' => !is_null($vehicleData['removalDate'])
        ];

        /** @var \Zend\Form\FormInterface $form */
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
                    'seedDate' => isset($formData['licence-vehicle']['warningLetterSeedDate'])
                        ? $formData['licence-vehicle']['warningLetterSeedDate'] : null,
                    'sentDate' =>  isset($formData['licence-vehicle']['warningLetterSentDate'])
                        ? $formData['licence-vehicle']['warningLetterSentDate'] : null,
                ];

                $dtoClass = $this->updateVehicleMap[$this->lva];

                $response = $this->handleCommand($dtoClass::create($dtoData));
            }

            if ($response->isOk()) {
                return $this->handlePostSave(null, ['query' => $request->getQuery()->toArray()]);
            }

            if ($response->isServerError()) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addCurrentErrorMessage('unknown-error');
            } else {
                $this->mapVehicleErrors($form, $response->getResult()['messages']);
            }
        }

        return $this->render('edit_vehicles', $form);
    }

    /**
     * Delete vehicles
     *
     * @return bool
     */
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

        if (!in_array($result['licenceType']['id'], $acceptedLicenceTypes, false)) {
            return 'delete.confirmation.text';
        }

        if ($result['activeVehicleCount'] > $toDelete) {
            return 'delete.confirmation.text';
        }

        return 'deleting.all.vehicles.message';
    }

    /**
     * Get delete modal title
     *
     * @return string
     */
    protected function getDeleteTitle()
    {
        return 'delete-vehicles';
    }

    /**
     * Process Reprint action
     *
     * @return \Common\View\Model\Section|\Zend\Http\Response
     */
    public function reprintAction()
    {
        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $ids = explode(',', $this->params('child_id'));

            $response = $this->handleCommand(ReprintDisc::create(['ids' => $ids]));

            if (!$response->isOk()) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
            }

            return $this->redirect()->toRouteAjax(
                $this->getBaseRoute(),
                [
                    $this->getIdentifierIndex() => $this->getIdentifier()
                ],
                [
                    'query' => $request->getQuery()->toArray()
                ],
                true
            );
        }

        $form = $this->getConfirmationForm($request);

        return $this->render('reprint_vehicles', $form);
    }

    /**
     * Transfer vehicles action
     *
     * @return mixed
     */
    public function transferAction()
    {
        return $this->transferVehicles();
    }

    /**
     * Render Form
     *
     * @param \Common\Form\Form $form       Form
     * @param array             $headerData Data from Api
     *
     * @return \Common\View\Model\Section
     */
    protected function renderForm($form, $headerData)
    {
        if ($headerData['spacesRemaining'] < 0) {
            $this->getServiceLocator()->get('Helper\Guidance')->append('more-vehicles-than-authorisation');
        }

        $files = ['lva-crud', 'vehicle-goods', 'vehicles'];
        $params = [
            'mainWrapperCssClass' => 'full-width',
        ];

        $searchForm = $this->getVehcileSearchForm($headerData);
        if ($searchForm) {
            $params['searchForm'] = $searchForm;
            $files[] = 'forms/vehicle-search';
        }

        $this->getServiceLocator()->get('Script')->loadFiles($files);

        return $this->render('vehicles', $form, $params);
    }

    /**
     * Build table
     *
     * @param array $headerData Data from Api
     * @param array $filters    Route parameters
     *
     * @return mixed
     */
    protected function getTable($headerData, $filters)
    {
        $query = $this->removeUnusedParametersFromQuery(
            (array)$this->getRequest()->getQuery()
        );
        $params = array_merge($query, ['query' => $query]);

        $tableName = 'lva-' . $this->location . '-vehicles';

        $table = $this->getServiceLocator()->get('Table')
            ->prepareTable($tableName, $headerData['licenceVehicles'], $params);

        $this->makeTableAlterations($table, $headerData, $filters);

        return $table;
    }

    /**
     * Make table alter
     *
     * @param TableBuilder $table   Table
     * @param array        $params  Changes parameters
     * @param array        $filters Route parameters
     *
     * @return void
     */
    protected function makeTableAlterations(TableBuilder $table, $params, $filters)
    {
        if (isset($params['canReprint']) && $params['canReprint']) {
            $table->addAction(
                'reprint',
                [
                    'label' => 'vehicle_table_action.reprint.label',
                    'requireRows' => true,
                    'class' => ' more-actions__item action--secondary',
                ]
            );
        }

        if (isset($params['canTransfer']) && $params['canTransfer']) {
            $table->addAction(
                'transfer',
                [
                    'label' => 'vehicle_table_action.transfer.label',
                    'class' => ' more-actions__item js-require--multiple action--secondary',
                    'requireRows' => true,
                ]
            );
        }

        if (isset($params['canExport']) && $params['canExport']) {
            $table->addAction(
                'export',
                [
                    'label' => 'vehicle_table_action.export.label',
                    'requireRows' => true,
                    'class' => ' more-actions__item js-disable-crud action--secondary',
                ]
            );
        }

        if (isset($params['canPrintVehicle']) && $params['canPrintVehicle']) {
            $table->addAction(
                'print-vehicles',
                [
                    'label' => 'vehicle_table_action.print-vehicles.label',
                    'requireRows' => true,
                    'class' => ' more-actions__item action--secondary',
                ]
            );
        }

        if (isset($params['allVehicleCount'])
            && isset($params['activeVehicleCount'])
            && (int)$params['allVehicleCount'] > (int)$params['activeVehicleCount']
        ) {
            $this->addRemovedVehiclesActions($filters, $table);
        }
    }

    /**
     * Show confirmation
     *
     * @param \Zend\Http\Request $request Htt Request
     *
     * @return mixed
     */
    protected function getConfirmationForm(\Zend\Http\Request $request)
    {
        return $this->getServiceLocator()->get('Helper\Form')
            ->createFormWithRequest('GenericConfirmation', $request);
    }

    /**
     * Define filters (query/route parameters)
     *
     * @return array
     */
    protected function getFilters()
    {
        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $query = $request->getPost('query');
        } else {
            $query = $request->getQuery();
        }

        return $this->formatFilters((array)$query);
    }

    /**
     * Format filters (query/route parameters)
     *
     * @param array $query parameters
     *
     * @return array
     */
    protected function formatFilters($query)
    {
        $filters = [
            'page'  => (isset($query['page']) ? $query['page'] : self::DEF_TABLE_FIRST_PAGE_NR),
            'limit' => (isset($query['limit']) ? $query['limit'] : self::DEF_TABLE_ITEMS_COUNT),
            'sort'  => isset($query['sort']) ? $query['sort'] : 'createdOn',
            'order' => isset($query['order']) ? $query['order'] : 'DESC',
        ];

        if (isset($query['vehicleSearch']['vrm']) && !isset($query['vehicleSearch']['clearSearch'])) {
            $filters['vrm'] = $query['vehicleSearch']['vrm'];
        }

        if (isset($query['specified']) && in_array($query['specified'], ['Y', 'N'], false)) {
            $filters['specified'] = $query['specified'];
        }

        $filters['includeRemoved'] = (isset($query['includeRemoved']) && $query['includeRemoved'] == '1');

        if (isset($query['disc']) && in_array($query['disc'], ['Y', 'N'], false)) {
            $filters['disc'] = $query['disc'];
        }

        return $filters;
    }

    /**
     * Get pre-configured form
     *
     * @param array $headerData Data from Api
     * @param array $formData   Data from Post
     *
     * @return mixed
     */
    protected function getForm($headerData, $formData)
    {
        return $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-goods-' . $this->section)
            ->getForm($this->getTable($headerData, $this->getFilters()))
            ->setData($formData);
    }

    /**
     * Map errors
     *
     * @param FormInterface $form   Form
     * @param array         $errors Error messages
     *
     * @return void
     */
    protected function mapErrors(\Zend\Form\FormInterface $form, array $errors)
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

    /**
     * Map vehicle errors
     *
     * @param FormInterface $form   Form
     * @param array         $errors Error messages
     *
     * @return void
     */
    protected function mapVehicleErrors(\Zend\Form\FormInterface $form, array $errors)
    {
        $errors = Mapper\Lva\GoodsVehiclesVehicle::mapFromErrors($errors, $form);

        if (!empty($errors)) {
            $fm = $this->getServiceLocator()->get('Helper\FlashMessenger');

            foreach ($errors as $error) {
                $fm->addCurrentErrorMessage($error);
            }
        }
    }

    /**
     * Format the confirmation message
     *
     * @param string $message Json message
     *
     * @return string
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
        }

        return $translator->translate('vehicle-belongs-to-another-licence-message-external');
    }

    /**
     * Post save
     *
     * @param \Common\View\Model\Section $section Section
     *
     * @return void
     */
    protected function postSave($section)
    {
        // @NOTE Prevents postSave from doing anything as this section has been migrated
    }

    /**
     * Get vehicle section data
     *
     * @return mixed
     */
    protected function getVehicleSectionData()
    {
        $dtoData = [
            'id'    => $this->getIdentifier(),
            'page'  => 1,
            'limit' => 1,
            'sort'  => 'createdOn',
            'order' => 'DESC'
        ];
        $dtoClass = $this->loadDataMap[$this->lva];
        $response = $this->handleQuery($dtoClass::create($dtoData));

        return $response->getResult();
    }
}
