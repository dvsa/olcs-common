<?php

/**
 * Vehicle Section
 *
 * Internal/External - Application/Licence - Vehicle Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\VehicleSafety;

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
                'properties' => array(
                    'id',
                    'receivedDate',
                    'specifiedDate',
                    'deletedDate'
                ),
                'children' => array(
                    'goodsDiscs' => array(
                        'ceasedDate',
                        'discNo'
                    ),
                    'vehicle' => array(
                        'properties' => array(
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
    protected $sharedActionService = 'LicenceVehicle';

    /**
     * Action data map
     *
     * @var array
     */
    protected $sharedActionDataMap = array(
        'main' => array(
            'mapFrom' => array(
                'data'
            ),
            'children' => array(
                'licence-vehicle' => array(
                    'mapFrom' => array(
                        'licence-vehicle'
                    )
                )
            )
        )
    );

    /**
     * Shared action data bundle
     *
     * @var array
     */
    protected $sharedActionDataBundle = array(
        'properties' => array(
            'id',
            'version',
            'receivedDate',
            'deletedDate',
            'specifiedDate'
        ),
        'children' => array(
            'goodsDiscs' => array(
                'properties' => array(
                    'discNo'
                )
            ),
            'vehicle' => array(
                'properties' => array(
                    'id',
                    'version',
                    'platedWeight',
                    'vrm'
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
     * Get action data bundle
     *
     * @return array
     */
    protected function getActionDataBundle()
    {
        return $this->sharedActionDataBundle;
    }

    /**
     * Alter form
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function doAlterActionForm($form)
    {
        return $this->genericActionFormAlterations($form);
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

        $licenceId = $this->getLicenceId();

        $data = $this->makeRestCall('Licence', 'GET', array('id' => $licenceId), $this->tableDataBundle);

        $results = array();

        if (isset($data['licenceVehicles']) && !empty($data['licenceVehicles'])) {

            foreach ($data['licenceVehicles'] as $licenceVehicle) {

                if (!$this->showVehicle($licenceVehicle)) {
                    continue;
                }

                $row = array_merge($licenceVehicle, $licenceVehicle['vehicle']);

                unset($row['vehicle']);
                unset($row['goodsDiscs']);

                $row['discNo'] = $this->getCurrentDiscNo($licenceVehicle);

                $results[] = $row;
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
        if ($this->getActionName() !== 'add') {
            $licenceVehicle = $data;
            unset($licenceVehicle['vehicle']);

            $licenceVehicle['discNo'] = $this->getCurrentDiscNo($licenceVehicle);
            unset($licenceVehicle['goodsDiscs']);

            $data = array(
                'licence-vehicle' => $licenceVehicle,
                'data' => $data['vehicle']
            );
        }

        return $data;
    }

    /**
     * Get current disc number
     *
     * @param array $licenceVehicle
     * @return string
     */
    protected function getCurrentDiscNo($licenceVehicle)
    {
        $discNo = '';

        if (isset($licenceVehicle['goodsDiscs']) && !empty($licenceVehicle['goodsDiscs'])) {
            foreach ($licenceVehicle['goodsDiscs'] as $discs) {
                if (empty($discs['ceasedDate'])) {
                    $discNo = $discs['discNo'];
                    break;
                }
            }
        }

        return $discNo;
    }

    /**
     * Performs delete action
     *
     * @return \Zend\Http\PhpEnvironment\Response
     */
    public function deleteAction()
    {
        return $this->delete();
    }

    /**
     * Hi-jack the crud action check, so we can validate whether they have enough vehicles or not
     *
     * @param string $action
     */
    protected function checkForAlternativeCrudAction($action)
    {
        if ($action == 'add') {
            // Check if we haven't already exceeded the total authorised vehicles
            // If so we need to add an error message and redirect back to where we are
        }
    }
}
