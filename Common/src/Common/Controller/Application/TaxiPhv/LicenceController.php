<?php

/**
 * Licence Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Controller\Application\TaxiPhv;

/**
 * Licence Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceController extends TaxiPhvController
{
    use \Common\Controller\Traits\TrafficAreaTrait;

    /**
     * Holds the sub action service
     *
     * @var string
     */
    protected $actionService = 'PrivateHireLicence';

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
     * Holds the actionDataBundle
     *
     * @var array
     */
    protected $actionDataBundle = array(
        'properties' => array(
            'id',
            'version',
            'privateHireLicenceNo',
        ),
        'children' => array(
            'contactDetails' => array(
                'properties' => array(
                    'id',
                    'version',
                    'description'
                ),
                'children' => array(
                    'address' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'addressLine1',
                            'addressLine2',
                            'addressLine3',
                            'addressLine4',
                            'postcode',
                            'town'
                        ),
                        'children' => array(
                            'countryCode' => array(
                                'properties' => array(
                                    'id'
                                )
                            )
                        )
                    )
                )
            )
        )
    );

    /**
     * Northern Ireland Traffic Area Code
     */
    const NORTHERN_IRELAND_TRAFFIC_AREA_CODE = 'N';

    /**
     * Holds the table data
     *
     * @var array
     */
    protected $tableData;

    /**
     * Form tables
     *
     * @var array
     */
    protected $formTables = array(
        'table' => 'application_taxi-phv_licence-form'
    );

    /**
     * Render the section form
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->renderSection();
    }

    /**
     * Add licence
     */
    public function addAction()
    {
        return $this->renderSection();
    }

    /**
     * Edit licence
     */
    public function editAction()
    {
        return $this->renderSection();
    }

    /**
     * Delete licence
     *
     * @return Response
     */
    public function deleteAction()
    {
        $this->maybeClearTrafficAreaId();
        return $this->delete();
    }

    /**
     * Get table data
     *
     * @return array
     */
    protected function getFormTableData($id, $table)
    {
        if (is_null($this->tableData)) {

            $licence = $this->getLicenceData();

            $data = $this->makeRestCall(
                'PrivateHireLicence',
                'GET',
                array('licence' => $licence['id']),
                $this->getActionDataBundle()
            );

            $newData = array();

            foreach ($data['Results'] as $row) {

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

        $trafficArea = $this->getTrafficArea();
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
                    $this->setTrafficArea($trafficAreaId);
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
            $this->setTrafficArea($data['trafficArea']);
        }
    }

    /**
     * Set up traffic area fields
     *
     * @param object $form
     * @return object
     */
    protected function alterForm($form)
    {
        // set up Traffic Area section
        $licencesExists = count($this->tableData);
        $trafficArea = $this->getTrafficArea();
        $trafficAreaId = $trafficArea ? $trafficArea['id'] : '';
        if (!$licencesExists) {
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
            $form->get('dataTrafficArea')->get('trafficArea')->setValueOptions($this->getTrafficValueOptions());
        }

        return $form;
    }

    /**
     * Alter form to process traffic area
     *
     * @param Form $form
     */
    protected function alterActionForm($form)
    {
        $form->getInputFilter()->get('address')->get('postcode')->setRequired(false);

        $trafficAreaValidator = $this->getServiceLocator()->get('postcodePhlTrafficAreaValidator');
        $licenceId = $form->get('data')->get('licence')->getValue();
        $trafficAreaValidator->setPrivateHireLicencesCount($this->getPrivateHireLicencesCount($licenceId));
        $trafficAreaValidator->setTrafficArea($this->getTrafficArea());

        $postcodeValidatorChain = $form->getInputFilter()->get('address')->get('postcode')->getValidatorChain();
        $postcodeValidatorChain->attach($trafficAreaValidator);

        if (!$this->getTrafficArea()) {
            $form->get('form-actions')->remove('addAnother');
        }
        return $form;
    }

    /**
     * Get operating centres count
     *
     * @param int $licenceId
     * @return int
     */
    public function getPrivateHireLicencesCount($licenceId = null)
    {
        if (!$licenceId) {
            $licence = $this->getLicenceData();
            if (is_array($licence) && isset($licence['id'])) {
                $licenceId = $licence['id'];
            }
        }

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
     * Clear Traffic Area if we are deleting last one operating centres
     */
    public function maybeClearTrafficAreaId()
    {
        $licCount = $this->getPrivateHireLicencesCount();
        if ($licCount == 1 && $this->getActionId()) {
            $this->setTrafficArea(null);
        }
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
        if (!$this->getTrafficArea()) {
            $dataTrafficArea = $this->params()->fromPost('dataTrafficArea');
            $trafficArea = is_array($dataTrafficArea) && isset($dataTrafficArea['trafficArea']) ?
                $dataTrafficArea['trafficArea'] : '';
            if ($action == 'add' && !$trafficArea && $this->getPrivateHireLicencesCount()) {
                $this->addWarningMessage('Please select a traffic area');
                return $this->redirectToRoute(null, array(), array(), true);
            } elseif ($action == 'add' && $trafficArea) {
                $this->setTrafficArea($trafficArea);
            }
        }

        return $this->redirect()->toRoute($route, $params, [], true);
    }
}
