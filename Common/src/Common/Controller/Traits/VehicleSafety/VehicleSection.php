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
                    'deletedDate',
                    'removalDate'
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

    protected $ceaseActiveDiscBundle = array(
        'properties' => array(),
        'children' => array(
            'goodsDiscs' => array(
                'properties' => array(
                    'id',
                    'version',
                    'ceasedDate'
                )
            )
        )
    );

    /**
     * Disc pending bundle
     */
    protected $discPendingBundle = array(
        'properties' => array(
            'id',
            'specifiedDate',
            'deletedDate'
        ),
        'children' => array(
            'goodsDiscs' => array(
                'ceasedDate',
                'discNo'
            )
        )
    );

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
     * Get bespoke sub actions
     *
     * @return array
     */
    protected function getBespokeSubActions()
    {
        return $this->sharedBespokeSubActions;
    }

    /**
     * Reprint action
     */
    public function reprintAction()
    {
        return $this->renderSection();
    }

    /**
     * Load data for reprint
     *
     * @param int $id
     * @return array
     */
    protected function reprintLoad($id)
    {
        return array(
            'data' => array(
                'id' => implode(',', (array)$id)
            )
        );
    }

    /**
     * Request a new disc
     *
     * @param array $data
     */
    protected function reprintSave($data)
    {
        $ids = explode(',', $data['data']['id']);

        foreach ($ids as $id) {
            $this->reprintDisc($id);
        }

        return $this->goBackToSection();
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
        $this->makeRestCall('GoodsDisc', 'POST', array('licenceVehicle' => $licenceVehicleId, 'isCopy' => $isCopy));
    }

    /**
     * If the latest disc is not active, cease it
     *
     * @param int $id
     */
    protected function ceaseActiveDisc($id)
    {
        $results = $this->makeRestCall('LicenceVehicle', 'GET', $id, $this->ceaseActiveDiscBundle);

        if (!empty($results['goodsDiscs'])) {
            $activeDisc = $results['goodsDiscs'][0];

            if (empty($activeDisc['ceasedDate'])) {
                $activeDisc['ceasedDate'] = date('Y-m-d H:i:s');
                $this->makeRestCall('GoodsDisc', 'PUT', $activeDisc);
            }
        }
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
     * Check if the disc is pending
     *
     * @param array $licenceVehicleData
     * @return boolean
     */
    protected function isDiscPending($licenceVehicleData)
    {
        if (empty($licenceVehicleData['specifiedDate']) && empty($licenceVehicleData['deletedDate'])) {
            return true;
        }

        if (isset($licenceVehicleData['goodsDiscs']) && !empty($licenceVehicleData['goodsDiscs'])) {
            $currentDisc = $licenceVehicleData['goodsDiscs'][0];

            if (empty($currentDisc['ceasedDate']) && empty($currentDisc['discNo'])) {

                return true;
            }
        }

        return false;
    }

    /**
     * Hijack the crud action check so we can validate the add button
     *
     * @param string $action
     */
    protected function checkForAlternativeCrudAction($action)
    {
        if ($action == 'reprint') {
            $id = $this->params()->fromPost('id');

            if ($this->isDiscPendingForLicenceVehicle($id)) {
                $this->addErrorMessage('reprint-pending-disc-error');
                return $this->goBackToSection();
            }
        }

        if ($action == 'add') {
            $totalAuth = $this->getTotalNumberOfAuthorisedVehicles();

            if (!is_numeric($totalAuth)) {
                return;
            }

            $vehicleCount = $this->getTotalNumberOfVehicles();

            if ($vehicleCount >= $totalAuth) {
                $this->addErrorMessage('more-vehicles-than-total-auth-error');
                return $this->redirect()->toRoute(null, array(), array(), true);
            }
        }
    }

    /**
     * Check if the licence vehicle has a pending active disc
     *
     * @param int $id
     * @return boolean
     */
    protected function isDiscPendingForLicenceVehicle($id)
    {
        $ids = (array)$id;

        foreach ($ids as $id) {
            $results = $this->makeRestCall('LicenceVehicle', 'GET', $id, $this->discPendingBundle);

            if ($this->isDiscPending($results)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Replace default process delete
     *
     * @param array $data
     */
    protected function deleteSave($data)
    {
        $licenceVehicleId = $data['data']['id'];

        $licenceVehicleIds = explode(',', $licenceVehicleId);

        foreach ($licenceVehicleIds as $id) {
            $this->ceaseActiveDisc($id);
            $result = $this->makeRestCall('LicenceVehicle', 'GET', $id, ['properties' => ['version']]);
            $this->makeRestCall(
                'LicenceVehicle',
                'PUT',
                ['id' => $id, 'version' => $result['version'], 'removalDate' => date('Y-m-d')]
            );
        }
    }
}
