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
     * Holds the bundle to retrieve VRM
     *
     * @var array
     */
    protected $vrmBundle = array(
        'properties' => array(),
        'children' => array(
            'vehicle' => array(
                'properties' => array(
                    'vrm'
                )
            )
        )
    );

    protected $vehiclePsvBundle = array(
        'properties' => array(
            'id',
            'version',
            'receivedDate',
            'deletedDate',
            'specifiedDate'
        ),
        'children' => array(
            'vehicle' => array(
                'properties' => array(
                    'id',
                    'version',
                    'vrm',
                    'isNovelty'
                ),
                'children' => array(
                    'psvType' => array(
                        'properties' => array(
                            'id'
                        )
                    )
                )
            )
        )
    );

    protected $currentVrmBundle = array(
        'properties' => array('vehicle'),
        'children' => array(
            'vehicle' => array(
                'properties' => array(
                    'vrm'
                )
            )
        )
    );

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
        $this->forcePut($id, array('removalDate' => date('Y-m-d')));
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
            $activeDisc['ceasedDate'] = date('Y-m-d');
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
        $data = $this->getServiceLocator()->get('Helper\Rest')
            ->makeRestCall(
                'LicenceVehicle',
                'GET',
                array(
                    'licence' => $licenceId,
                    'removalDate' => 'NULL',
                    'limit' => 'all'
                ),
                $this->currentVrmBundle
            );

        $vrms = array();

        foreach ($data['Results'] as $row) {
            $vrms[] = $row['vehicle']['vrm'];
        }

        return $vrms;
    }
}
