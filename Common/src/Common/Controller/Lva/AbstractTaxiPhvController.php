<?php

/**
 * Shared logic between Taxi Phv controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Form\Elements\Validators\PrivateHireLicenceTrafficAreaValidator;

/**
 * Shared logic between Taxi Phv controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
abstract class AbstractTaxiPhvController extends AbstractController
{
    use Traits\CrudTableTrait {
        Traits\CrudTableTrait::handleCrudAction as genericHandleCrudAction;
    }

    private $data;

    protected $tableData;

    protected $section = 'taxi_phv';

    public function indexAction()
    {
        $this->loadData();

        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->getFormData();
        }

        $form = $this->getForm()->setData($data);

        $this->alterFormForLva($form);

        if ($request->isPost()) {

            $crudAction = $this->getCrudAction(array($data['table']));

            if ($crudAction !== null) {
                $this->getServiceLocator()->get('Helper\Form')->disableEmptyValidation($form);
            }

            if ($form->isValid()) {

                $this->save($data);

                if ($crudAction !== null) {
                    return $this->handleCrudAction($crudAction);
                }

                return $this->completeSection('taxi_phv');
            }
        }

        $this->getServiceLocator()->get('Script')->loadFile('lva-crud');

        return $this->render('taxi_phv', $form);
    }

    protected function handleCrudAction($data)
    {
        $action = $this->getActionFromCrudAction($data);

        $trafficArea = $this->getTrafficArea();

        // if traffic area is not set and add clicked then make sure traffic area is chosen
        if (empty($trafficArea)) {
            $dataTrafficArea = $this->params()->fromPost('dataTrafficArea');

            $trafficArea = is_array($dataTrafficArea) && isset($dataTrafficArea['trafficArea'])
                ? $dataTrafficArea['trafficArea'] : '';

            if ($action == 'add' && empty($trafficArea) && $this->getPrivateHireLicencesCount() > 0) {

                $this->getServiceLocator()->get('Helper\FlashMessenger')
                    ->addWarningMessage('Please select a traffic area');

                return $this->reload();

            } elseif ($action == 'add' && !empty($trafficArea)) {
                $this->updateTrafficArea($trafficArea);
            }
        }

        return $this->genericHandleCrudAction($data);
    }

    /**
     * Save method
     *
     * @param array $data
     */
    protected function save($data)
    {
        // update trafiic area if selected
        if (isset($data['dataTrafficArea']['trafficArea']) && !empty($data['dataTrafficArea']['trafficArea'])) {
            $this->updateTrafficArea($data['dataTrafficArea']['trafficArea']);

        }

        // update completion if application/variation
        if ($this->lva !== 'licence') {
            $this->handleCommand(
                \Dvsa\Olcs\Transfer\Command\Application\UpdateCompletion::create(
                    ['id' => $this->getIdentifier(), 'section' => 'taxiPhv']
                )
            );
        }
    }

    protected function getForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $formHelper->createForm('Lva\TaxiPhv');

        $formHelper->populateFormTable($form->get('table'), $this->getTable());

        return $this->alterForm($form);
    }

    protected function getFormData()
    {
        return ['dataTrafficArea' => $this->getTrafficArea()];
    }

    protected function getTable()
    {
        return $this->getServiceLocator()->get('Table')->prepareTable('lva-taxi-phv', $this->getTableData());
    }

    protected function getTableData()
    {
        if ($this->tableData === null) {

            $data = $this->getPrivateHireLicences();

            $newData = array();
            foreach ($data as $row) {

                $newRow = array(
                    'id' => $row['id'],
                    'privateHireLicenceNo' => $row['privateHireLicenceNo'],
                    'councilName' => $row['contactDetails']['description']
                );

                unset($row['contactDetails']['address']['id']);
                unset($row['contactDetails']['address']['version']);

                $newData[] = array_merge($newRow, $row['contactDetails']['address']);
            }

            $this->tableData = $newData;
        }

        return $this->tableData;
    }

    protected function alterForm($form)
    {
        $licenceTableData = $this->getTableData();

        $trafficArea = $this->getTrafficArea();

        $trafficAreaId = $trafficArea ? $trafficArea['id'] : '';

        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        // remove enforcement area as not required
        $formHelper->remove($form, 'dataTrafficArea->enforcementArea');

        if (empty($licenceTableData)) {
            $formHelper->remove($form, 'dataTrafficArea');
        } elseif ($trafficAreaId) {
            $formHelper->remove($form, 'dataTrafficArea->trafficArea');
            $form->get('dataTrafficArea')->get('trafficAreaSet')
                ->setValue($trafficArea['name'])
                ->setOption('hint-suffix', '-taxi-phv');
        } else {
            $formHelper->remove($form, 'dataTrafficArea->trafficAreaSet');

            $form->get('dataTrafficArea')->get('trafficArea')->setValueOptions(
                $this->getTrafficAreaOptions()
            );
        }

        return $form;
    }

    public function addAction()
    {
        return $this->addOrEdit('add');
    }

    public function editAction()
    {
        return $this->addOrEdit('edit');
    }

    protected function addOrEdit($mode)
    {
        $this->loadData();

        $request = $this->getRequest();

        $data = array();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } elseif ($mode === 'edit') {
            $id = $this->params('child_id');
            $data = $this->getPrivateHireLicence($id);
        }

        if (!$request->isPost()) {
            $data = $this->formatDataForLicenceForm($mode, $data);
        }

        $form = $this->getLicenceForm()->setData($data);

        if ($mode === 'edit') {
            $form->get('form-actions')->remove('addAnother');
        }

        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $hasProcessed = $formHelper->processAddressLookupForm($form, $this->getRequest());
        // (don't validate or proceed if we're just processing the postcode lookup)

        if (!$hasProcessed && $request->isPost() && $form->isValid()) {
            $this->saveLicence($data);

            return $this->handlePostSave();
        }

        return $this->render($mode . '_taxi_phv', $form);
    }

    protected function getLicenceForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $formHelper->createFormWithRequest('Lva\TaxiPhvLicence', $this->getRequest());
        return $this->alterActionForm($form);
    }

    /**
     * Alter form to process traffic area
     *
     * @param Form $form
     */
    protected function alterActionForm($form)
    {
        $form->getInputFilter()->get('address')->get('postcode')->setRequired(false);

        $trafficArea = $this->getTrafficArea();
        $trafficAreaValidator = new PrivateHireLicenceTrafficAreaValidator();
        $trafficAreaValidator->setServiceLocator($this->getServiceLocator());

        $trafficAreaValidator->setPrivateHireLicencesCount(
            $this->getPrivateHireLicencesCount()
        );

        $trafficAreaValidator->setTrafficArea($trafficArea);

        $postcodeValidatorChain = $form->getInputFilter()->get('address')->get('postcode')->getValidatorChain();
        $postcodeValidatorChain->attach($trafficAreaValidator);

        if (!$trafficArea && $form->get('form-actions')->has('addAnother')) {
            $form->get('form-actions')->remove('addAnother');
        }
        return $form;
    }

    /**
     * Delete licence
     *
     * @return Response
     */
    public function delete()
    {
        $ids = explode(',', $this->params('child_id'));

        if ($this->lva === 'licence') {
            $command = \Dvsa\Olcs\Transfer\Command\PrivateHireLicence\DeleteList::create(
                ['ids' => $ids, 'licence' => $this->getLicenceId(), 'lva' => $this->lva]
            );
        } else {
            $command = \Dvsa\Olcs\Transfer\Command\Application\DeleteTaxiPhv::create(
                ['id' => $this->getIdentifier(), 'ids' => $ids, 'licence' => $this->getLicenceId(), 'lva' => $this->lva]
            );
        }
        $response = $this->handleCommand($command);
        if (!$response->isOk()) {
            throw new \RuntimeException('Failed to delete PrivateHireLicence');
        }
    }

    /**
     * Get count of the number of private hire licences
     *
     * @return int
     */
    protected function getPrivateHireLicencesCount()
    {
        return count($this->getPrivateHireLicences());

    }

    /**
     * Process the action load data
     *
     * @param array $oldData
     */
    protected function formatDataForLicenceForm($mode, $oldData)
    {
        $data['data'] = $oldData;

        if ($mode !== 'add') {
            $data['contactDetails'] = $oldData['contactDetails'];
            $data['address'] = $oldData['contactDetails']['address'];
            $data['address']['countryCode'] = $data['address']['countryCode']['id'];
        }

        $trafficArea = $this->getTrafficArea();
        if (isset($trafficArea['id'])) {
            $data['trafficArea'] = $trafficArea['id'];
        }

        return $data;
    }

    /**
     * Save the licence
     *
     * @param array $data
     * @param string $service
     * @return null|Response
     */
    protected function saveLicence($data)
    {
        if (is_numeric($data['contactDetails']['id'])) {
            $this->update($data);
        } else {
            $this->create($data);
        }

        // set default Traffic Area if we don't have one
        if (!isset($data['trafficArea'])
            || empty($data['trafficArea']['id'])
            && $data['address']['postcode']
        ) {

            $licencesCount = $this->getPrivateHireLicencesCount();
            // first Licence was just added or we are editing the first one
            if ($licencesCount === 0) {
                $postcodeService = $this->getServiceLocator()->get('postcode');

                try {
                    $trafficAreaId = $postcodeService->getTrafficAreaByPostcode($data['address']['postcode'])[0];
                } catch (\Exception $e) {
                    // handle error from postcode service, just don't set traffic area
                    $trafficAreaId = null;
                }
                if (!empty($trafficAreaId)) {
                    $this->updateTrafficArea($trafficAreaId);
                }
            }
        }
    }

    /**
     * Create a new PrivateHireLicence
     *
     * @param array $formData
     * @throws \RuntimeException
     */
    protected function create($formData)
    {
        $params = [
            'id' => $this->getIdentifier(),
            'licence' => $this->getLicenceId(),
            'lva' => $this->lva,
            'privateHireLicenceNo' => $formData['data']['privateHireLicenceNo'],
            'councilName' => $formData['contactDetails']['description'],
            'address' => [
                'addressLine1' => $formData['address']['addressLine1'],
                'addressLine2' => $formData['address']['addressLine2'],
                'addressLine3' => $formData['address']['addressLine3'],
                'addressLine4' => $formData['address']['addressLine4'],
                'town' => $formData['address']['town'],
                'postcode' => $formData['address']['postcode'],
                'countryCode' => $formData['address']['countryCode'],
            ]
        ];

        if ($this->lva === 'licence') {
            $command = \Dvsa\Olcs\Transfer\Command\PrivateHireLicence\Create::create($params);
        } else {
            $command = \Dvsa\Olcs\Transfer\Command\Application\CreateTaxiPhv::create($params);
        }

        $response = $this->handleCommand($command);
        if (!$response->isOk()) {
            throw new \RuntimeException('Failed creating privateHireLicence');
        }
    }

    /**
     * Update a new PrivateHireLicence
     *
     * @param array $formData
     * @throws \RuntimeException
     */
    protected function update($formData)
    {
        $params = [
            'version' => $formData['data']['version'],
            'licence' => $this->getLicenceId(),
            'lva' => $this->lva,
            'privateHireLicenceNo' => $formData['data']['privateHireLicenceNo'],
            'councilName' => $formData['contactDetails']['description'],
            'address' => [
                'addressLine1' => $formData['address']['addressLine1'],
                'addressLine2' => $formData['address']['addressLine2'],
                'addressLine3' => $formData['address']['addressLine3'],
                'addressLine4' => $formData['address']['addressLine4'],
                'town' => $formData['address']['town'],
                'postcode' => $formData['address']['postcode'],
                'countryCode' => $formData['address']['countryCode'],
            ]
        ];

        if ($this->lva === 'licence') {
            $params['id'] = $this->params('child_id');
            $command = \Dvsa\Olcs\Transfer\Command\PrivateHireLicence\Update::create($params);
        } else {
            $params['id'] = $this->getIdentifier();
            $params['privateHireLicence'] = $this->params('child_id');
            $command = \Dvsa\Olcs\Transfer\Command\Application\UpdateTaxiPhv::create($params);
        }

        $response = $this->handleCommand($command);
        if (!$response->isOk()) {
            throw new \RuntimeException('Failed updating privateHireLicence');
        }
    }

    /**
     * Load Taxi/PHV data, this is required for subsequent calls
     *
     * @throws \Exception
     * @throws \RuntimeException
     */
    private function loadData()
    {
        if ($this->lva === 'licence') {
            $query = \Dvsa\Olcs\Transfer\Query\Licence\TaxiPhv::create(['id' => $this->getIdentifier()]);
        } else {
            $query = \Dvsa\Olcs\Transfer\Query\Application\TaxiPhv::create(['id' => $this->getIdentifier()]);
        }

        $response = $this->handleQuery($query);
        if (!$response->isOk()) {
            throw new \RuntimeException('Error getting taxi/phv licences');
        }

        $this->data = $response->getResult();
    }

    /**
     * Get the Traffic Area data for the licence
     *
     * @return array
     */
    private function getTrafficArea()
    {
        return (isset($this->data['licence'])) ?
            $this->data['licence']['trafficArea'] :
            $this->data['trafficArea'];
    }

    /**
     * Get data all Private Vehicles Licences
     *
     * @return array
     */
    private function getPrivateHireLicences()
    {
        return (isset($this->data['licence'])) ?
            $this->data['licence']['privateHireLicences'] :
            $this->data['privateHireLicences'];
    }

    /**
     * Get data for one Private Vehicle Licence
     *
     * @param int $id
     *
     * @return array|false
     */
    private function getPrivateHireLicence($id)
    {
        foreach ($this->getPrivateHireLicences() as $phl) {
            if ($phl['id'] == $id) {
                return $phl;
            }
        }
        return false;
    }

    /**
     * Get a list of Traffic Areas from use in a Select
     *
     * @return array
     */
    private function getTrafficAreaOptions()
    {
        return $this->data['trafficAreaOptions'];
    }

    /**
     * Get the Licence ID
     *
     * @return int
     */
    protected function getLicenceId($applicationId = null)
    {
        // parameter is required by parent
        unset($applicationId);

        return (isset($this->data['licence'])) ?
            $this->data['licence']['id'] :
            $this->data['id'];
    }

    /**
     * Get the Licence version
     *
     * @return int
     */
    protected function getLicenceVersion()
    {
        return (isset($this->data['licence'])) ?
            $this->data['licence']['version'] :
            $this->data['version'];
    }

    /**
     * Update the Traffic Area of a licence
     *
     * @param string $trafficAreaId TrafficAreaId eg B, C
     */
    protected function updateTrafficArea($trafficAreaId)
    {
        $this->handleCommand(
            \Dvsa\Olcs\Transfer\Command\Licence\UpdateTrafficArea::create(
                [
                    'id' => $this->getLicenceId(),
                    'version' => $this->getLicenceVersion(),
                    'trafficArea' => $trafficAreaId
                ]
            )
        );
    }
}
