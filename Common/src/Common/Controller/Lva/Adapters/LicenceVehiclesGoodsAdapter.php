<?php

/**
 * Licence Vehicles Goods Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Interfaces\VehicleGoodsAdapterInterface;

/**
 * Licence Vehicles Goods Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceVehiclesGoodsAdapter extends AbstractAdapter implements VehicleGoodsAdapterInterface
{
    public function getFilteredVehiclesData($id, $query)
    {
        $filters = [];

        if (isset($query['vrm'])) {
            // Where the VRM starts with the 'vrm' string
            $filters['vrm'] = '~' . $query['vrm'] . '%';
        }

        $filters['specified'] = 'NOT NULL';

        if (isset($query['includeRemoved']) && $query['includeRemoved'] == 1) {
            $filters['removalDate'] = 'NOT NULL';
        } else {
            $filters['removalDate'] = 'NULL';
        }

        if (isset($query['disc'])) {
            // Has active discs
            if ($query['disc'] === 'Y') {
                $filters['disc'] = '';
            }

            // Has no active discs
            if ($query['disc'] === 'N') {
                $filters['disc'] = '';
            }
        }

        $this->getServiceLocator()->get('Entity\Licence')->getVehiclesData($id);
    }

    public function getFormData($id)
    {
        return [];
    }
}
