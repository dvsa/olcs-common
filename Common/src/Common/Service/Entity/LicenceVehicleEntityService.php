<?php

/**
 * Licence Vehicle Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Licence Vehicle Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceVehicleEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'LicenceVehicle';

    protected $vehicleBundle = array(
        'children' => array(
            'goodsDiscs',
            'vehicle'
        )
    );

    protected $ceaseActiveDiscBundle = array(
        'children' => array(
            'goodsDiscs'
        )
    );

    /**
     * Disc pending bundle
     */
    protected $discPendingBundle = array(
        'children' => array(
            'goodsDiscs'
        )
    );

    /**
     * Holds the bundle to retrieve VRM
     *
     * @var array
     */
    protected $vrmBundle = array(
        'children' => array(
            'vehicle'
        )
    );


    protected $currentVrmBundle = array(
        'children' => array(
            'vehicle'
        )
    );

    protected $vehiclePsvBundle = array(
        'children' => array(
            'vehicle' => array(
                'children' => array(
                    'psvType'
                )
            )
        )
    );

    protected $applicationValidationBundle = array('properties' => array('id'));

    public function getVehicle($id)
    {
        return $this->get($id, $this->vehicleBundle);
    }

    public function getVehiclePsv($id)
    {
        return $this->get($id, $this->vehiclePsvBundle);
    }

    /**
     * Delete functionality just sets the removal date for licence vehicle
     *
     * @param int $id
     */
    public function delete($id)
    {
        $date = $this->getServiceLocator()->get('Helper\Date')->getDate();

        $this->forcePut($id, array('removalDate' => $date));
    }

    /**
     * Cease the active disc
     *
     * @param int $id
     */
    public function ceaseActiveDisc($id)
    {
        $results = $this->get($id, $this->ceaseActiveDiscBundle);

        if (empty($results['goodsDiscs'])) {
            return;
        }

        $activeDisc = $results['goodsDiscs'][0];

        if (empty($activeDisc['ceasedDate'])) {
            $date = $this->getServiceLocator()->get('Helper\Date')->getDate();
            $activeDisc['ceasedDate'] = $date;
            $this->getServiceLocator()->get('Entity\GoodsDisc')->save($activeDisc);
        }
    }

    public function getDiscPendingData($id)
    {
        return $this->get($id, $this->discPendingBundle);
    }

    public function getVrm($id)
    {
        return $this->get($id, $this->vrmBundle)['vehicle']['vrm'];
    }

    public function getCurrentVrmsForLicence($licenceId)
    {
        $data = array(
            'licence' => $licenceId,
            'removalDate' => 'NULL'
        );

        $results = $this->getAll($data, $this->currentVrmBundle);

        $vrms = array();

        foreach ($results['Results'] as $row) {
            $vrms[] = $row['vehicle']['vrm'];
        }

        return $vrms;
    }

    public function getForApplicationValidation($licenceId)
    {
        $query = array(
            'licence' => $licenceId,
            'removalDate' => 'NULL'
        );

        $results = $this->getAll($query, $this->applicationValidationBundle);

        return $results['Results'];
    }
}
