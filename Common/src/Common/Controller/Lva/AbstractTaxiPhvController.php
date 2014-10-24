<?php

/**
 * Shared logic between Taxi Phv controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

/**
 * Shared logic between Taxi Phv controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractTaxiPhvController extends AbstractController
{
    use Traits\CrudTableTrait;

    protected $tableData;

    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = array();
        }

        $form = $this->getForm()->setData($data);

        $this->alterFormForLocation($form);
        $this->alterFormForLva($form);

        if ($request->isPost()) {

            $crudAction = $this->getCrudAction(array($data['table']));

            if ($crudAction !== null) {
                $this->getServiceLocator()->get('Helper\Form')->disableEmptyValidation($form);
            }

            if ($form->isValid()) {

                $this->postSave('taxi_phv');

                if ($crudAction !== null) {
                    return $this->handleCrudAction($crudAction);
                }

                return $this->completeSection('taxi_phv');
            }
        }

        return $this->render('taxi_phv', $form);
    }

    protected function getForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $formHelper->createForm('Lva\TaxiPhv');

        $formHelper->populateFormTable($form->get('table'), $this->getTable());

        return $this->alterForm($form);
    }

    protected function getTable()
    {
        return $this->getServiceLocator()->get('Table')->prepareTable('lva-taxi-phv', $this->getTableData());
    }

    protected function getTableData()
    {
        if ($this->tableData === null) {

            $data = $this->getServiceLocator()->get('Entity\PrivateHireLicence')->getByLicenceId($this->getLicenceId());

            $newData = array();

            foreach ($data as $row) {

                $newRow = array(
                    'id' => $row['id'],
                    'privateHireLicenceNo' => $row['privateHireLicenceNo'],
                    'councilName' => $row['contactDetails']['description']
                );

                // Not sure if this is significant
                //unset($row['contactDetails']['address']['id']);
                //unset($row['contactDetails']['address']['version']);

                $newData[] = array_merge($newRow, $row['contactDetails']['address']);
            }

            $this->tableData = $newData;
        }

        return $this->tableData;
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
        $request = $this->getRequest();

        $data = array();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } elseif ($mode === 'edit') {
            $data = $this->getLicenceFormData();
        }

        $form = $this->getLicenceForm()->setData($data);

        if ($mode === 'edit') {
            $form->get('form-actions')->remove('addAnother');
        }

        if ($request->isPost() && $form->isValid()) {

            $this->saveLicence($data);

            return $this->handlePostSave();
        }

        return $this->render($mode . '_taxi_phv', $form);
    }

    protected function getLicenceForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\TaxiPhvLicence');
    }

    protected function getLicenceFormData()
    {
        return array();
    }

    protected function alterForm($form)
    {
        $licenceTableData = $this->getTableData();

        $trafficArea = $this->getSectionService('TrafficArea')->getTrafficArea();
        $trafficAreaId = $trafficArea ? $trafficArea['id'] : '';

        if (!empty($licenceTableData)) {
            $form->remove('dataTrafficArea');
        } elseif ($trafficAreaId) {
            $form->get('dataTrafficArea')->remove('trafficArea');
            $template = $form->get('dataTrafficArea')->get('trafficAreaInfoNameExists')->getValue();
            $newValue = str_replace('%NAME%', $trafficArea['name'], $template);
            $form->get('dataTrafficArea')->get('trafficAreaInfoNameExists')->setValue($newValue);
        } else {
            $form->get('dataTrafficArea')->remove('trafficAreaInfoLabelExists');
            $form->get('dataTrafficArea')->remove('trafficAreaInfoNameExists');
            $form->get('dataTrafficArea')->remove('trafficAreaInfoHintExists');
            $form->get('dataTrafficArea')->get('trafficArea')->setValueOptions(
                $this->getSectionService('TrafficArea')->getTrafficAreaValueOptions()
            );
        }

        return $form;
    }

    /**
     * Delete licence
     *
     * @todo These 2 methods need altering to allow multiple delete
     *
     * @return Response
     */
    public function delete()
    {
        $this->maybeClearTrafficAreaId();

        // Do delete
    }

    /**
     * Clear Traffic Area if we are deleting last one operating centres
     */
    protected function maybeClearTrafficAreaId()
    {
        $licCount = $this->getPrivateHireLicencesCount();
        if ($licCount == 1 && $this->getActionId()) {
            $this->getSectionService('TrafficArea')->setTrafficArea(null);
        }
    }

    /**
     * Get operating centres count
     *
     * @param int $licenceId
     * @return int
     */
    protected function getPrivateHireLicencesCount()
    {
        $licenceId = $this->getLicenceId();

        $licencesCount = 0;

        if ($licenceId) {
            $bundle = array(
                'properties' => array(
                    'id',
                    'version'
                )
            );
            $privateHireLicences = $this->makeRestCall(
                'PrivateHireLicence',
                'GET',
                array(
                    'licence' => $licenceId,
                ),
                $bundle
            );
            $licencesCount = $privateHireLicences['Count'];
        }
        return $licencesCount;
    }









    /**
     * Holds the data bundle
     *
     * @var array
     */
    protected $dataBundle = array(
        'properties' => array(
            'id',
            'version',
        ),
        'children' => array(
            'licence' => array(
                'properties' => array(
                    'id'
                ),
                'children' => array(
                    'trafficArea' => array(
                        'properties' => array(
                            'id',
                            'name'
                        )
                    )
                )
            )
        )
    );

    /**
     * Data map
     *
     * @var array
     */
    protected $dataMap = array(
        'main' => array(
            'mapFrom' => array(
                'data',
                'dataTrafficArea'
            ),
        ),
    );

    /**
     * Action data map
     *
     * @var array
     */
    protected $actionDataMap = array(
        '_addresses' => array(
            'address'
        ),
        'main' => array(
            'children' => array(
                'privateHireLicence' => array(
                    'mapFrom' => array(
                        'data'
                    )
                ),
                'contactDetails' => array(
                    'mapFrom' => array(
                        'contactDetails'
                    ),
                    'children' => array(
                        'addresses' => array(
                            'mapFrom' => array(
                                'addresses'
                            )
                        )
                    )
                )
            )
        )
    );

    /**
     * Process the action load data
     *
     * @param array $oldData
     */
    protected function processActionLoad($oldData)
    {
        $data['data'] = $oldData;

        if ($this->getActionName() != 'add') {

            $data['contactDetails'] = $oldData['contactDetails'];
            $data['address'] = $oldData['contactDetails']['address'];
            $data['address']['countryCode'] = $data['address']['countryCode']['id'];
        }

        $licenceData = $this->getLicenceData();

        $data['data']['licence'] = $licenceData['id'];

        $trafficArea = $this->getSectionService('TrafficArea')->getTrafficArea();
        if (isset($trafficArea['id'])) {
            $data['trafficArea']['id'] = $trafficArea['id'];
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
    protected function actionSave($data, $service = null)
    {
        $data['contactDetails']['contactType'] = 'ct_council';

        $results = parent::actionSave($data['contactDetails'], 'ContactDetails');

        if (!empty($data['contactDetails']['id'])) {
            $contactDetailsId = $data['contactDetails']['id'];
        } elseif (isset($results['id'])) {
            $contactDetailsId = $results['id'];
        } else {
            /**
             * Handle failure to save contactDetails. For now we just throw an exception until the story has been
             * complete which encompassess feeding back errors to the user
             */
            throw new \Exception('Unable to save contact details');
        }

        $data['privateHireLicence']['contactDetails'] = $contactDetailsId;
        $saved = parent::actionSave($data['privateHireLicence'], $service);
        if ($this->getActionName() == 'add' && !isset($saved['id'])) {
            throw new \Exception('Unable to save licence');
        }

        // set default Traffic Area if we don't have one
        if (!array_key_exists('trafficArea', $data) || !$data['trafficArea']['id'] &&
            $data['contactDetails']['addresses']['address']['postcode']) {
            $licencesCount = $this->getPrivateHireLicencesCount($data['privateHireLicence']['licence']);
            // first Licence was just added or we are editing the first one
            if ($licencesCount == 1) {
                $postcodeService = $this->getPostcodeService();
                list($trafficAreaId, $trafficAreaName) =
                    $postcodeService->getTrafficAreaByPostcode(
                        $data['contactDetails']['addresses']['address']['postcode']
                    );
                if ($trafficAreaId) {
                    $this->getSectionService('TrafficArea')->setTrafficArea($trafficAreaId);
                }
            }
        }
    }

    /**
     * Save method
     *
     * @param array $data
     */
    protected function save($data, $service = null)
    {
        if (isset($data['trafficArea']) && $data['trafficArea']) {
            $this->getSectionService('TrafficArea')->setTrafficArea($data['trafficArea']);
        }
    }

    /**
     * Alter form to process traffic area
     *
     * @param Form $form
     */
    protected function alterActionForm($form)
    {
        $form->getInputFilter()->get('address')->get('postcode')->setRequired(false);

        $trafficArea = $this->getSectionService('TrafficArea')->getTrafficArea();

        $trafficAreaValidator = $this->getServiceLocator()->get('postcodePhlTrafficAreaValidator');
        $licenceId = $form->get('data')->get('licence')->getValue();
        $trafficAreaValidator->setPrivateHireLicencesCount($this->getPrivateHireLicencesCount($licenceId));
        $trafficAreaValidator->setTrafficArea($trafficArea);

        $postcodeValidatorChain = $form->getInputFilter()->get('address')->get('postcode')->getValidatorChain();
        $postcodeValidatorChain->attach($trafficAreaValidator);

        if (!$trafficArea) {
            $form->get('form-actions')->remove('addAnother');
        }
        return $form;
    }

    /**
     * Method to allow adding new Licence only if Traffic Area has been set
     *
     * @param string $route
     * @param array $params
     * @param string $itemIdParam
     *
     * @return boolean
     */
    public function checkForCrudAction($route = null, $params = array(), $itemIdParam = 'id')
    {
        $table = $this->params()->fromPost('table');
        $action = isset($table['action']) && !is_array($table['action'])
            ? strtolower($table['action'])
            : strtolower($this->params()->fromPost('action'));

        if (empty($action)) {
            return false;
        }

        $params = array_merge($params, array('action' => $action));

        if ($action !== 'add') {
            $id = $this->params()->fromPost('id');

            if (empty($id)) {

                return false;
            }

            $params[$itemIdParam] = $id;
        }
        if (!$this->getSectionService('TrafficArea')->getTrafficArea()) {
            $dataTrafficArea = $this->params()->fromPost('dataTrafficArea');
            $trafficArea = is_array($dataTrafficArea) && isset($dataTrafficArea['trafficArea']) ?
                $dataTrafficArea['trafficArea'] : '';
            if ($action == 'add' && !$trafficArea && $this->getPrivateHireLicencesCount()) {
                $this->addWarningMessage('Please select a traffic area');
                return $this->redirectToRoute(null, array(), array(), true);
            } elseif ($action == 'add' && $trafficArea) {
                $this->getSectionService('TrafficArea')->setTrafficArea($trafficArea);
            }
        }

        return $this->redirect()->toRoute($route, $params, [], true);
    }
}
