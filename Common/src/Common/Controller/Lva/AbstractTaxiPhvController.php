<?php

namespace Common\Controller\Lva;

use Common\Form\Form;
use Common\FormService\FormServiceManager;
use Common\Service\Cqrs\Response as CqrsResponse;

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
    protected $baseRoute = 'lva-%s/taxi_phv';

    /**
     * Index action
     *
     * @return array|\Common\View\Model\Section|\Laminas\Http\Response
     */
    public function indexAction()
    {
        try {
            $this->loadData();
        } catch (\RuntimeException $ex) {
            return $this->notFoundAction();
        }

        /** @var \Laminas\Http\Request $request */
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
                if ($this->isInternalReadOnly()) {
                    return $this->handleCrudAction($crudAction);
                }

                $this->getServiceLocator()->get('Helper\Form')->disableEmptyValidation($form);
            }

            if ($form->isValid()) {
                if (
                    $this->lva !== 'licence' &&
                    ($crudAction == null || $this->getActionFromCrudAction($crudAction) == 'add') &&
                    !$this->save($data)
                ) {
                    return $this->reload();
                }

                if ($crudAction !== null) {
                    return $this->handleCrudAction($crudAction);
                }

                return $this->completeSection('taxi_phv');
            }
        }

        $this->getServiceLocator()->get('Script')->loadFile('lva-crud');

        return $this->render('taxi_phv', $form);
    }

    /**
     * handle crud action
     *
     * @param array $data data
     *
     * @return \Laminas\Http\Response
     */
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
                    ->addErrorMessage('Please select a traffic area');

                return $this->reload();
            }
        }

        return $this->genericHandleCrudAction($data);
    }

    /**
     * Save the Taxi Phv page
     *
     * @param array $data Form data
     *
     * @return boolean
     */
    protected function save($data)
    {
        $commandData = [
            'id' => $this->getIdentifier(),
            'trafficArea' => $data['dataTrafficArea']['trafficArea'] ?? null
        ];

        $response = $this->handleCommand(
            \Dvsa\Olcs\Transfer\Command\Application\UpdateTaxiPhv::create($commandData)
        );

        if ($response->isOk()) {
            return true;
        }

        if ($response->isServerError()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addUnknownError();
        }

        $messages = $response->getResult()['messages'];

        if (!empty($messages)) {
            $translator = $this->getServiceLocator()->get('Helper\Translation');
            $fm = $this->getServiceLocator()->get('Helper\FlashMessenger');

            foreach ($messages as $key => $message) {
                $fm->addErrorMessage(
                    $translator->translateReplace($key . '_' . strtoupper($this->location), $message)
                );
            }
        }

        return false;
    }

    /**
     * get form
     *
     * @return Form
     */
    protected function getForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $this->getServiceLocator()
            ->get(FormServiceManager::class)
            ->get('lva-' . $this->lva . '-' . $this->section)
            ->getForm();

        $formHelper->populateFormTable($form->get('table'), $this->getTable());

        return $this->alterForm($form);
    }

    /**
     * Get form data
     *
     * @return array
     */
    protected function getFormData()
    {
        return ['dataTrafficArea' => $this->getTrafficArea()];
    }

    /**
     * Get table
     *
     * @return \Common\Service\Table\TableBuilder
     */
    protected function getTable()
    {
        return $this->getServiceLocator()->get('Table')->prepareTable('lva-taxi-phv', $this->getTableData());
    }

    /**
     * Get table data
     *
     * @return array
     */
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

    /**
     * alter form
     *
     * @param Form $form form to be altered
     *
     * @return Form
     */
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

    /**
     * add action
     *
     * @return \Common\View\Model\Section|\Laminas\Http\Response
     */
    public function addAction()
    {
        return $this->addOrEdit('add');
    }

    /**
     * edit action
     *
     * @return \Common\View\Model\Section|\Laminas\Http\Response
     */
    public function editAction()
    {
        return $this->addOrEdit('edit');
    }

    /**
     * add or edit
     *
     * @param string $mode option to add or edit
     *
     * @return \Common\View\Model\Section|\Laminas\Http\Response
     */
    protected function addOrEdit($mode)
    {
        $this->loadData();

        /** @var \Laminas\Http\Request $request */
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

        $form = $this->getLicenceForm($mode)->setData($data);

        if ($mode === 'edit') {
            $form->get('form-actions')->remove('addAnother');
        }

        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $hasProcessed = $formHelper->processAddressLookupForm($form, $this->getRequest());
        // (don't validate or proceed if we're just processing the postcode lookup)

        if (!$hasProcessed && $request->isPost() && $form->isValid()) {
            $result = $this->saveLicence($data);
            if (is_array($result)) {
                $formMessages['address']['postcode'][] = $this->getTrafficAreaValidationMessage($result);
                $form->setMessages($formMessages);
            } elseif ($result === true) {
                return $this->handlePostSave();
            } else {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addCurrentUnknownError();
            }
        }

        return $this->render($mode . '_taxi_phv', $form);
    }

    /**
     * get licence form
     *
     * @param string $mode add or edit
     *
     * @return Form
     */
    protected function getLicenceForm($mode)
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $formHelper->createFormWithRequest('Lva\TaxiPhvLicence', $this->getRequest());
        return $this->alterActionForm($form, $mode);
    }

    /**
     * Alter form to process traffic area
     *
     * @param Form   $form form being altered
     * @param string $mode edit or add
     *
     * @return Form
     */
    protected function alterActionForm($form, $mode)
    {
        $form->getInputFilter()->get('address')->get('postcode')->setRequired(false);

        $trafficArea = $this->getTrafficArea();

        if (!$trafficArea && $form->get('form-actions')->has('addAnother')) {
            $form->get('form-actions')->remove('addAnother');
        }
        return $form;
    }

    /**
     * Delete licence
     *
     * @return CqrsResponse
     * @throws \RuntimeException
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
     * @param string $mode    add or edit
     * @param array  $oldData old data
     *
     * @return array
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
     * @param array $data licence data
     *
     * @return bool|array Array of error messages, false = the request failed, true = everything was good
     */
    protected function saveLicence($data)
    {
        if (is_numeric($data['contactDetails']['id'])) {
            $response = $this->update($data);
        } else {
            $response = $this->create($data);
        }

        if ($response->isClientError()) {
            return $response->getResult()['messages'];
        }

        return $response->isOk();
    }

    /**
     * Create a new PrivateHireLicence
     *
     * @param array $formData form data
     *
     * @return CqrsResponse
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

        return $this->handleCommand($command);
    }

    /**
     * get traffic area validation message
     *
     * @param array $message message array
     *
     * @return array
     */
    private function getTrafficAreaValidationMessage(array $message)
    {
        $translator = $this->getServiceLocator()->get('Helper\Translation');
        if (key($message) === 'PHL_INVALID_TA') {
            return $message;
        }
        $result = current($message);
        if (!is_array($result)) {
            $result = [$result];
        }
        return $translator->translateReplace(key($message) . '_' . strtoupper($this->location), $result);
    }

    /**
     * Update a new PrivateHireLicence
     *
     * @param array $formData form data
     *
     * @return CqrsResponse
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
            $command = \Dvsa\Olcs\Transfer\Command\Application\UpdatePrivateHireLicence::create($params);
        }

        return $this->handleCommand($command);
    }

    /**
     * Load Taxi/PHV data, this is required for subsequent calls
     *
     * @return array
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
     * @param int $id id
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
     * @param int|null $applicationId application id
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
}
