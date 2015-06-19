<?php

/**
 * Goods Vehicles
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Data\Mapper\Lva\GoodsVehicles;
use Common\Data\Mapper\Lva\GoodsVehiclesVehicle;
use Common\Service\Table\Formatter\VehicleDiscNo;
use Dvsa\Olcs\Transfer\Command\Application\CreateGoodsVehicle as ApplicationCreateGoodsVehicle;
use Dvsa\Olcs\Transfer\Command\Licence\CreateGoodsVehicle as LicenceCreateGoodsVehicle;
use Dvsa\Olcs\Transfer\Command\Application\UpdateGoodsVehicle as ApplicationUpdateGoodsVehicle;
use Dvsa\Olcs\Transfer\Command\Vehicle\UpdateGoodsVehicle as LicenceUpdateGoodsVehicle;
use Dvsa\Olcs\Transfer\Command\Application\UpdateVehicles;
use Dvsa\Olcs\Transfer\Query\LicenceVehicle\LicenceVehicle;
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

                $action = $this->getActionFromCrudAction($crudAction);

                $alternativeCrudAction = $this->checkForAlternativeCrudAction($action);

                if ($alternativeCrudAction === null) {
                    return $this->handleCrudAction($crudAction, array('add', 'print-vehicles'));
                }

                return $alternativeCrudAction;
            }

            return $this->completeSection('vehicles');
        }

        return $this->renderForm($form, $headerData);
    }

    public function addAction()
    {
        $request = $this->getRequest();
        $data = [];

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        }

        $dtoData = [
            'id' => $this->getIdentifier(),
            'page' => 1,
            'limit' => 1
        ];
        $dtoClass = $this->loadDataMap[$this->lva];
        $response = $this->handleQuery($dtoClass::create($dtoData));

        $params = [];
        $params['spacesRemaining'] = $response->getResult()['spacesRemaining'];

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

                    $confirm = new Checkbox('confirm-add', array('label' => 'vehicle-belongs-to-another-licence-confirmation'));

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

        if ($request->isPost() && $form->isValid()) {
            $formData = $form->getData();

            $dtoData = [
                $this->getIdentifierIndex() => $this->getIdentifier(),
                'id' => $id,
                'version' => $formData['data']['version'],
                'platedWeight' => $formData['data']['platedWeight']
            ];

            $dtoClass = $this->updateVehicleMap[$this->lva];

            $response = $this->handleCommand($dtoClass::create($dtoData));

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
     * @todo migrate this
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
}
