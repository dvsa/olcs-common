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

    public function getVehicle($id)
    {
        return $this->get($id, $this->vehicleBundle);
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
}
