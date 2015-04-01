<?php

/**
 * Application Vehicles Goods Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Interfaces\VehicleGoodsAdapterInterface;

/**
 * Application Vehicles Goods Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationVehiclesGoodsAdapter extends AbstractAdapter implements VehicleGoodsAdapterInterface
{
    public function getFilteredVehiclesData($id, $query)
    {
        $filters = [
            'page' => isset($query['page']) ? $query['page'] : 1,
            'limit' => isset($query['limit']) && is_numeric($query['limit']) ? $query['limit'] : 10,
        ];

        if (isset($query['vrm']) && $query['vrm'] !== 'All') {
            // Where the VRM starts with the 'vrm' string
            $filters['vrm'] = '~' . $query['vrm'] . '%';
        }

        if (isset($query['specified'])) {

            if ($query['specified'] === 'Y') {
                $filters['specifiedDate'] = 'NOT NULL';
            }

            if ($query['specified'] === 'N') {
                $filters['specifiedDate'] = 'NULL';
            }
        }

        if (isset($query['includeRemoved']) && $query['includeRemoved'] == '1') {
            $filters['removalDate'] = 'NOT NULL';
        } else {
            $filters['removalDate'] = 'NULL';
        }

        if (isset($query['disc'])) {
            // Has active discs
            if ($query['disc'] === 'Y') {
                $filters['disc'] = 'Y';
            }

            // Has no active discs
            if ($query['disc'] === 'N') {
                $filters['disc'] = 'N';
            }
        }

        return $this->getServiceLocator()->get('Entity\LicenceVehicle')->getVehiclesDataForApplication($id, $filters);
    }

    /**
     * Populate form with data
     */
    public function getFormData($id)
    {
        return $this->formatDataForForm(
            $this->getServiceLocator()->get('Entity\Application')->getHeaderData($id)
        );
    }

    /**
     * Format data for the main form; not a lot to it
     */
    protected function formatDataForForm($data)
    {
        return array(
            'data' => array(
                'version' => $data['version'],
                'hasEnteredReg' => $data['hasEnteredReg'] === 'N' ? 'N' : 'Y'
            )
        );
    }
}
