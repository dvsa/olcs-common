<?php

/**
 * Licence Vehicles Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

/**
 * Licence Vehicles Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait LicenceVehiclesControllerTrait
{
    /**
     * We only want to show active vehicles
     *
     * @param array $licenceVehicle
     * @return boolean
     */
    protected function showVehicle(array $licenceVehicle)
    {
        return (!empty($licenceVehicle['specifiedDate']) || empty($licenceVehicle['removalDate']));
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
     * Disc pending bundle
     */
    protected $discPendingBundle = array(
        'properties' => array(
            'id',
            'specifiedDate',
            'removalDate'
        ),
        'children' => array(
            'goodsDiscs' => array(
                'ceasedDate',
                'discNo'
            )
        )
    );

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
     * @NOTE I have put this logic into it's own method (rather in the reprintSave method), as we will soon be able to
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
     * Check if the disc is pending
     *
     * @param array $licenceVehicleData
     * @return boolean
     */
    protected function isDiscPending($licenceVehicleData)
    {
        if (empty($licenceVehicleData['specifiedDate']) && empty($licenceVehicleData['removalDate'])) {
            return true;
        }

        if (isset($licenceVehicleData['goodsDiscs']) && !empty($licenceVehicleData['goodsDiscs'])) {
            $currentDisc = $licenceVehicleData['goodsDiscs'][0];

            return (empty($currentDisc['ceasedDate']) && empty($currentDisc['discNo']));
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

        // @NOTE there was some irrelevent stuff here
    }
}
