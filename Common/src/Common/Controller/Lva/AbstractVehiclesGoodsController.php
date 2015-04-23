<?php

/**
 * Goods Vehicles Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

/**
 * Goods Vehicles Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractVehiclesGoodsController extends AbstractVehiclesController
{
    protected $section = 'vehicles';

    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $formData = (array)$request->getPost();
            $crudAction = $this->getCrudAction(array($formData['table']));
            $haveCrudAction = $crudAction !== null;
        } else {
            $formData = $this->getAdapter()->getFormData($this->getIdentifier());
            $haveCrudAction = false;
        }

        $formData = array_merge(
            $formData,
            [
                'query' => (array)$this->getRequest()->getQuery()
            ]
        );

        // Get preconfigured form
        $form = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-goods-' . $this->section)
            ->getForm($this->getTable(), $haveCrudAction)
            ->setData($formData);

        if ($request->isPost() && $form->isValid()) {

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

            $this->postSave('vehicles');

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

        // Check if the user is attempting to edit a removed vehicle, and bail early if so
        if ($mode === 'edit' && $request->isPost()) {

            $vehicleData = $this->getVehicleFormData($id);

            if (isset($vehicleData['removalDate']) && !empty($vehicleData['removalDate'])) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')
                    ->addErrorMessage('cant-edit-removed-vehicle');

                return $this->redirect()->toRoute(null, [], [], true);
            }
        }

        $data = array();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } elseif ($mode === 'edit') {
            $vehicleData = $this->getVehicleFormData($id);
            $data = $this->formatVehicleDataForForm($vehicleData);
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

        $licenceVehicle['discNo'] = $this->getCurrentDiscNo($licenceVehicle);
        unset($licenceVehicle['goodsDiscs']);

        return array(
            'licence-vehicle' => $licenceVehicle,
            'data' => $data['vehicle']
        );
    }

    protected function getTable()
    {
        $query = $this->getRequest()->getQuery();
        $params = array_merge((array)$query, ['query' => $query]);

        return $this->alterTable(
            $this->getServiceLocator()->get('Table')
                ->prepareTable('lva-vehicles', $this->getTableData(), $params)
        );
    }

    /**
     * Alter table.
     */
    protected function alterTable($table)
    {
        return $this->getAdapter()->alterVehcileTable($table, $this->getIdentifier());
    }

    protected function getTableData()
    {
        if ($this->getRequest()->isPost()) {
            $query = $this->getRequest()->getPost('query');
        } else {
            $query = $this->getRequest()->getQuery();
        }

        $licenceVehicles = $this->getAdapter()->getFilteredVehiclesData(
            $this->getIdentifier(),
            (array)$query
        );

        $results = array();

        foreach ($licenceVehicles['Results'] as $licenceVehicle) {

            // watch out! Now we get *all* data back, this was overrriding
            // the licence vehicle ID incorrectly
            unset($licenceVehicle['vehicle']['id']);

            $row = array_merge($licenceVehicle, $licenceVehicle['vehicle']);

            unset($row['vehicle']);
            unset($row['goodsDiscs']);

            $row['discNo'] = $this->getCurrentDiscNo($licenceVehicle);

            $results[] = $row;
        }

        $licenceVehicles['Results'] = $results;

        return $licenceVehicles;
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
