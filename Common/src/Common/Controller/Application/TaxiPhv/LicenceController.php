<?php

/**
 * Licence Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Application\TaxiPhv;

/**
 * Licence Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceController extends TaxiPhvController
{
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
     * Holds the Traffic Area details
     *
     * @var array
     */
    private $trafficArea;

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
    protected function getFormTableData()
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
             * @todo Handle failure to save contactDetails. For now we just throw an exception until the story has been
             * complete which encompassess feeding back errors to the user
             */
            throw new \Exception('Unable to save contact details');
        }

        $data['privateHireLicence']['contactDetails'] = $contactDetailsId;

        parent::actionSave($data['privateHireLicence'], $service);
    }

    /**
     * Save method
     *
     * @param array $data
     * @param string $service
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
     * Get operating centres count
     *
     * @return int
     */
    public function getPrivateHireLicencesCount()
    {
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
                'application' => $this->getIdentifier(),
            ),
            $bundle
        );
        return $privateHireLicences['Count'];
    }    


    
    /**
     * Get Traffic Area value options for select element
     *
     * @return array
     */
    protected function getTrafficValueOptions()
    {
        $bundle = array(
            'properties' => array(
                'id',
                'name',
            ),
        );

        $trafficArea = $this->makeRestCall('TrafficArea', 'GET', array(), $bundle);
        $valueOptions = array();
        $results = $trafficArea['Results'];
        if (is_array($results) && count($results)) {
            usort(
                $results,
                function ($a, $b) {
                    return strcmp($a["name"], $b["name"]);
                }
            );

            // remove Northern Ireland Traffic Area
            foreach ($results as $key => $value) {
                if ($value['id'] == self::NORTHERN_IRELAND_TRAFFIC_AREA_CODE) {
                    unset($results[$key]);
                    break;
                }
            }

            foreach ($results as $element) {
                $valueOptions[$element['id']] = $element['name'];
            }
        }
        return $valueOptions;
    }
    
    /**
     * Get Traffic Area information for current application
     *
     * @return array
     */
    protected function getTrafficArea()
    {
        if (!$this->trafficArea) {
            $bundle = array(
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

            $application = $this->makeRestCall(
                'Application',
                'GET',
                array(
                    'id' => $this->getIdentifier(),
                ),
                $bundle
            );
            if (is_array($application) && array_key_exists('licence', $application) &&
                is_array($application['licence']) &&
                array_key_exists('trafficArea', $application['licence'])) {
                $this->trafficArea = $application['licence']['trafficArea'];
            }
        }
        return $this->trafficArea;
    }

    /**
     * Set traffic area to application's licence based on traarea id
     *
     * @param string $id
     */
    public function setTrafficArea($id = null)
    {
        $bundle = array(
            'properties' => array(
                'id',
                'version'
            ),
            'children' => array(
                'licence' => array(
                    'properties' => array(
                        'id',
                        'version'
                    )
                )
            )
        );
        $application = $this->makeRestCall('Application', 'GET', array('id' => $this->getIdentifier()), $bundle);
        if (is_array($application) && array_key_exists('licence', $application) &&
            array_key_exists('version', $application['licence'])) {
            $data = array(
                        'id' => $application['licence']['id'],
                        'version' => $application['licence']['version'],
                        'trafficArea' => $id
            );
            $this->makeRestCall('Licence', 'PUT', $data);
        }
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
    
}
