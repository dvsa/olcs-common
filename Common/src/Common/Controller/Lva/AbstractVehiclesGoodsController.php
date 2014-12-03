<?php

/**
 * Goods Vehicles Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Form\Elements\Validators\NewVrm;

/**
 * Goods Vehicles Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractVehiclesGoodsController extends AbstractVehiclesController
{
    use Traits\CrudTableTrait;

    protected $section = 'vehicles';

    /**
     * Index action
     *
     * @return Response
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        $filterForm = $this->getFilterForm();

        if ($request->isPost()) {

            $this->postSave('vehicles');

            $data = (array)$request->getPost();
            $crudAction = $this->getCrudAction(array($data['table']));

            if ($crudAction !== null) {

                $action = $this->getActionFromCrudAction($crudAction);

                $alternativeCrudAction = $this->checkForAlternativeCrudAction($action);

                if ($alternativeCrudAction === null) {
                    return $this->handleCrudAction($crudAction, array('add', 'print-vehicles'));
                }

                return $alternativeCrudAction;
            }

            return $this->completeSection('vehicles');
        }

        $form = $this->getForm();

        $this->alterFormForLva($form);

        $this->getServiceLocator()->get('Script')->loadFiles(['lva-crud', 'forms/filter', 'table-actions']);

        // *always* check if the user has exceeded their authority
        // as a nice little addition; they may have changed their OC totals
        if ($this->getTotalNumberOfVehicles() > $this->getTotalNumberOfAuthorisedVehicles()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addWarningMessage(
                'more-vehicles-than-authorisation'
            );
        }

        return $this->render('vehicles', $form, array('filterForm' => $filterForm));
    }

    /**
     * Add vehicle
     */
    public function addAction()
    {
        return $this->addOrEdit('add');
    }

    /**
     * Edit vehicle
     */
    public function editAction()
    {
        return $this->addOrEdit('edit');
    }

    /**
     * Common add / edit logic
     */
    protected function addOrEdit($mode)
    {
        $request = $this->getRequest();
        $id = $this->params('child_id');
        $data = array();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } elseif ($mode === 'edit') {
            $data = $this->formatVehicleDataForForm($this->getVehicleFormData($id));
        }

        $form = $this->alterVehicleForm(
            $this->getVehicleForm()->setData($data),
            $mode
        );

        if ($request->isPost() && $form->isValid()) {

            // If we are in edit mode, we can save
            // If we are in add mode, and we have confirmed add, we can save
            // If we are in add mode, and haven't confirmed add, but the VRM is new we can save
            if ($mode === 'edit'
                || (isset($data['licence-vehicle']['confirm-add']) && !empty($data['licence-vehicle']['confirm-add']))
                || !$this->checkIfVehicleExistsOnOtherLicences($data, $form)
            ) {

                $data = $data = $this->getServiceLocator()->get('Helper\Data')
                    ->processDataMap($form->getData(), $this->vehicleDataMap);

                $this->saveVehicle($data, $mode);

                return $this->handlePostSave();
            }
        }

        // set default date values prior to render
        $today = new \DateTime();
        $form = $this->setDefaultDates($form, $today);

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

    protected function getVehicleForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\GoodsVehiclesVehicle');
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
        return $this->alterTable(
            $this->getServiceLocator()->get('Table')->prepareTable('lva-vehicles', $this->getTableData())
        );
    }

    /**
     * Alter table. No-op but is extended in certain sections
     */
    protected function alterTable($table)
    {
        return $table;
    }

    protected function getTableData()
    {
        $licenceId = $this->getLicenceId();
        $filters = $this->getGoodsVehcileFilters();

        $licenceVehicles = $this->getServiceLocator()->get('Entity\Licence')->getVehiclesData($licenceId);

        $results = array();

        foreach ($licenceVehicles as $licenceVehicle) {

            if (!$this->showVehicle($licenceVehicle, $filters)) {
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
     * Get goods vehicle filters
     *
     * @return array
     */
    protected function getGoodsVehcileFilters()
    {
        $filters = [];
        $filters['vrm'] = $this->params()->fromQuery('vrm', 'All');
        $filters['specified'] = $this->params()->fromQuery('specified', 'A');
        $filters['includeRemoved'] = $this->params()->fromQuery('includeRemoved', 0);
        $filters['disc'] = $this->params()->fromQuery('disc', 'A');
        return $filters;
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
                array($this->getIdentifierIndex() => $this->getIdentifier())
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

    /**
     * Get filter vehicle form
     *
     * @return Zend\Form\Form
     */
    protected function getFilterForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $form = $formHelper->createForm('Lva\VehicleFilter');
        $vrmOptions = array_merge(['All' => 'All'], array_combine(range('A', 'Z'), range('A', 'Z')));
        $form->get('vrm')->setValueOptions($vrmOptions);
        return $form;
    }

    /**
     * We only want to show active vehicles which
     * haven't been marked as removed
     *
     * @param array $licenceVehicle
     * @param array $filters
     * @return boolean
     */

    protected function showVehicle(array $licenceVehicle, array $filters = [])
    {
        $retv = true;
        if ($filters['vrm'] !== 'All' && substr($licenceVehicle['vehicle']['vrm'], 0, 1) !== $filters['vrm']) {
            $retv = false;
        }
        if ($filters['specified'] == 'Y' && empty($licenceVehicle['specifiedDate'])) {
            $retv = false;
        }
        if ($filters['specified'] == 'N' && !empty($licenceVehicle['specifiedDate'])) {
            $retv = false;
        }
        if (empty($filters['includeRemoved']) && !empty($licenceVehicle['removalDate'])) {
            $retv = false;
        }
        if ($filters['disc'] == 'Y' && !$this->hasActiveDisc($licenceVehicle)) {
            $retv = false;
        }
        if ($filters['disc'] == 'N' && $this->hasActiveDisc($licenceVehicle)) {
            $retv = false;
        }
        return $retv;
    }

    /**
     * Check if the vehicle has an active disc
     *
     * @param array $licenceVehicle
     * @return boolean
     */
    public function hasActiveDisc($licenceVehicle = [])
    {
        $hasDisc = false;
        foreach ($licenceVehicle['goodsDiscs'] as $goodsDisc) {
            if (!empty($goodsDisc['discNo'])) {
                $hasDisc = true;
                break;
            }
        }
        return $hasDisc;
    }
}
