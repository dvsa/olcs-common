<?php

/**
 * Vehicle Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits;

/**
 * Vehicle Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait VehicleSection
{
    use GenericVehicleSection;

    /**
     * Holds the table data bundle
     *
     * @var array
     */
    protected $tableDataBundle = array(
        'properties' => null,
        'children' => array(
            'licenceVehicles' => array(
                'properties' => null,
                'children' => array(
                    'vehicle' => array(
                        'properties' => array(
                            'id',
                            'vrm',
                            'platedWeight'
                        )
                    )
                )
            )
        )
    );

    /**
     * Action service
     *
     * @var string
     */
    protected $sharedActionService = 'Vehicle';

    /**
     * Action data map
     *
     * @var array
     */
    protected $sharedActionDataMap = array(
        'main' => array(
            'mapFrom' => array(
                'data'
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
     * Get action service
     *
     * @return string
     */
    protected function getActionService()
    {
        return $this->sharedActionService;
    }

    /**
     * Get action data map
     *
     * @return array
     */
    protected function getActionDataMap()
    {
        return $this->sharedActionDataMap;
    }

    /**
     * Get table data
     *
     * @param int $id
     * @return array
     */
    protected function getTableData($id)
    {
        unset($id);

        $licence = $this->getLicenceData();

        $data = $this->makeRestCall('Licence', 'GET', array('id' => $licence['id']), $this->tableDataBundle);

        $results = array();

        if (isset($data['licenceVehicles']) && !empty($data['licenceVehicles'])) {

            foreach ($data['licenceVehicles'] as $licenceVehicle) {

                if (isset($licenceVehicle['vehicle']) && !empty($licenceVehicle['vehicle'])) {
                    $results[] = $licenceVehicle['vehicle'];
                }
            }
        }

        return $results;
    }

    /**
     * Placeholder for save
     *
     * @param array $data
     * @parem string $service
     */
    protected function save($data, $service = null)
    {
    }

    /**
     * We don't need to load anything as there is no form
     *
     * @param int $id
     * @return array
     */
    protected function load($id)
    {
        return array();
    }

    /**
     * Format the data for the form
     *
     * @param array $data
     * @return array
     */
    protected function processActionLoad($data)
    {
        return array('data' => $data);
    }

    /**
     * Save the vehicle
     *
     * @todo might be able to combine these 2 methods now
     *
     * @param array $data
     * @param string $service
     */
    protected function actionSave($data, $service = null)
    {
        $this->saveVehicle($data, $this->getActionName());
    }

    /**
     * Performs delete action
     *
     * @return \Zend\Http\PhpEnvironment\Response
     */
    public function deleteAction()
    {
        $vehicleId = $this->getActionId();

        $cond = array(
            'vehicle' => $vehicleId,
            'licence' => $this->getLicenceId(),
        );

        $bundle = array(
            'properties' => array(
                'id'
            )
        );

        $licenceVehicle = $this->makeRestCall('LicenceVehicle', 'GET', $cond, $bundle);

        if (empty($licenceVehicle) || (isset($licenceVehicle['Count']) && $licenceVehicle['Count'] == 0)) {
            return $this->notFoundAction();
        }

        $this->makeRestCall('LicenceVehicle', 'DELETE', array('id' => $licenceVehicle['Results'][0]['id']));

        return $this->goBackToSection();
    }
}
