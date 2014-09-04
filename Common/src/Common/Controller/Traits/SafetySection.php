<?php

/**
 * Safety Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits;

/**
 * Safety Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait SafetySection
{
    /**
     * Holds the shared actoin data bundle
     *
     * @var array
     */
    protected $sharedActionDataBundle = array(
        'properties' => array(
            'id',
            'version',
            'isExternal'
        ),
        'children' => array(
            'contactDetails' => array(
                'properties' => array(
                    'id',
                    'version',
                    'fao'
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
                            'town',
                            'postcode'
                        ),
                        'children' => array(
                            'countryCode' => array(
                                'properties' => array('id')
                            )
                        )
                    )
                )
            )
        )
    );

    /**
     * Shared action data map
     *
     * @var array
     */
    protected $sharedActionDataMap = array(
        '_addresses' => array(
            'address'
        ),
        'main' => array(
            'children' => array(
                'workshop' => array(
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
     * Redirect to the first section
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->renderSection();
    }

    /**
     * Add operating centre
     */
    public function addAction()
    {
        return $this->renderSection();
    }

    /**
     * Edit operating centre
     */
    public function editAction()
    {
        return $this->renderSection();
    }

    /**
     * Delete sub action
     *
     * @return Response
     */
    public function deleteAction()
    {
        return $this->delete();
    }

    /**
     * Get form tables
     *
     * @return array
     */
    protected function getFormTables()
    {
        return array(
            'table' => 'safety-inspection-providers'
        );
    }

    /**
     * Get action service
     *
     * @return string
     */
    protected function getActionService()
    {
        return 'Workshop';
    }

    /**
     * Get action data bundle
     *
     * @return array
     */
    protected function getActionDataBundle()
    {
        return $this->sharedActionDataBundle;
    }

    /**
     * Get the action data map
     *
     * @return array
     */
    protected function getActionDataMap()
    {
        return $this->sharedActionDataMap;
    }

    /**
     * Shared logic for process load
     *
     * @param array $data
     * @return array
     */
    protected function doProcessLoad($data)
    {
        if (isset($data['licence']['tachographIns']['id'])) {
            $data['licence']['tachographIns'] = $data['licence']['tachographIns']['id'];
        }

        $data['application'] = array(
            'id' => $data['id'],
            'version' => $data['version'],
            'safetyConfirmation' => $data['safetyConfirmation'],
            'isMaintenanceSuitable' => $data['isMaintenanceSuitable']
        );

        unset($data['id']);
        unset($data['version']);
        unset($data['safetyConfirmation']);
        unset($data['isMaintenanceSuitable']);

        $data['licence']['safetyInsVehicles'] = 'inspection_interval_vehicle.' . $data['licence']['safetyInsVehicles'];
        $data['licence']['safetyInsTrailers'] = 'inspection_interval_trailer.' . $data['licence']['safetyInsTrailers'];

        return $data;
    }

    /**
     * Shared logic to get form table data
     *
     * @param array $data
     * @return array
     */
    protected function doGetFormTableData($data)
    {
        $tableData = array();

        foreach ($data as $workshop) {

            $row = $workshop;

            if (isset($row['contactDetails'])) {

                $row = array_merge($row, $row['contactDetails']);
                unset($row['contactDetails']);
            }

            if (isset($row['address'])) {

                $row = array_merge($row, $row['address']);
                unset($row['address']);
            }

            $tableData[] = $row;
        }

        return $tableData;
    }

    /**
     * Shared logic to alter a form
     *
     * @param Form $form
     * @param boolean $hideInternalFormElements
     * @param boolean $isPsv
     * @return Form
     */
    protected function doAlterForm($form, $hideInternalFormElements, $isPsv)
    {
        if ($hideInternalFormElements) {
            $form->get('application')->remove('isMaintenanceSuitable');
        }

        if ($isPsv) {
            $form->get('licence')->remove('safetyInsTrailers');

            $label = $form->get('licence')->get('safetyInsVaries')->getLabel();
            $form->get('licence')->get('safetyInsVaries')->setLabel($label . '.psv');

            $table = $form->get('table')->get('table')->getTable();

            $emptyMessage = $table->getVariable('empty_message');
            $table->setVariable('empty_message', $emptyMessage . '-psv');

            $form->get('table')->get('table')->setTable($table);
        }

        return $form;
    }

    /**
     * Shared logic to save licence
     *
     * @param array $data
     * @return array
     */
    protected function formatSaveData($data)
    {
        $data['licence']['safetyInsVehicles'] = str_replace(
            'inspection_interval_vehicle.', '', $data['licence']['safetyInsVehicles']
        );

        if (isset($data['licence']['safetyInsTrailers'])) {
            $data['licence']['safetyInsTrailers'] = str_replace(
                'inspection_interval_trailer.', '', $data['licence']['safetyInsTrailers']
            );
        }

        // Need to explicitly set these to null, otherwise empty string gets converted to 0
        if (array_key_exists('safetyInsTrailers', $data['licence']) && empty($data['licence']['safetyInsTrailers'])) {
            $data['licence']['safetyInsTrailers'] = null;
        }

        if (array_key_exists('safetyInsVehicles', $data['licence']) && empty($data['licence']['safetyInsVehicles'])) {
            $data['licence']['safetyInsVehicles'] = null;
        }

        return $data;
    }

    /**
     * Gets the data map
     *
     * @return array
     */
    protected function getDataMap()
    {
        return null;
    }

    /**
     * Action save
     *
     * @param array $data
     * @param string $service
     */
    protected function actionSave($data, $service = null)
    {
        $data['contactDetails']['contactType'] = 'ct_work';
        $saved = parent::actionSave($data['contactDetails'], 'ContactDetails');

        if ($this->getActionName() == 'add') {

            if (!isset($saved['id'])) {
                throw new \Exception('Unable to save contact details');
            }

            $data['workshop']['contactDetails'] = $saved['id'];
        }

        parent::actionSave($data['workshop'], 'Workshop');
    }

    /**
     * Process action load data
     *
     * @param array $data
     * @return array
     */
    protected function processActionLoad($data)
    {
        if ($this->getActionName() != 'add') {
            $data['data'] = array(
                'id' => $data['id'],
                'version' => $data['version'],
                'isExternal' => $data['isExternal']
            );

            $data['address'] = $data['contactDetails']['address'];
            $data['address']['countryCode'] = $data['address']['countryCode']['id'];

            unset($data['id']);
            unset($data['version']);
            unset($data['isExternal']);
            unset($data['contactDetails']['address']);
        }

        $data['data']['licence'] = $this->getLicenceId();

        return $data;
    }
}
