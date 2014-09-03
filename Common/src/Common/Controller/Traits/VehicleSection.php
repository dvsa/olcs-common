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
                'properties' => array(
                    'id',
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
    protected function alterActionForm($form)
    {
        $dataFieldset = $form->get('licence-vehicle');

        $this->disableDateElement($dataFieldset->get('specifiedDate'));
        $this->disableDateElement($dataFieldset->get('deletedDate'));
        $dataFieldset->get('discNo')->setAttribute('disabled', 'disabled');

        return $form;
    }

    /**
     * Disable date element
     *
     * @param \Zend\Form\Element\DateSelect $element
     */
    protected function disableDateElement($element)
    {
        $element->getDayElement()->setAttribute('disabled', 'disabled');
        $element->getMonthElement()->setAttribute('disabled', 'disabled');
        $element->getYearElement()->setAttribute('disabled', 'disabled');
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

                if (!$this->showVehicle($licenceVehicle['vehicle'])) {
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
     * This is extended in the licence section
     *
     * @param array $vehicle
     * @return boolean
     */
    protected function showVehicle($vehicle)
    {
        return true;
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
        $licenceVehicle = $data;
        unset($licenceVehicle['vehicle']);

        $licenceVehicle['discNo'] = $this->getCurrentDiscNo($licenceVehicle);
        unset($licenceVehicle['goodsDiscs']);

        $data = array(
            'licence-vehicle' => $licenceVehicle,
            'data' => $data['vehicle']
        );

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
        return $this->delete();
    }
}
