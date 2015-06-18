<?php

/**
 * Application Vehicles Goods Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

/**
 * Application Vehicles Goods Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationVehiclesGoodsAdapter extends AbstractAdapter
{
    public function getFilteredVehiclesData($id, $query)
    {
        $filters = $this->formatFilters($query);

        return $this->getServiceLocator()->get('Entity\LicenceVehicle')->getVehiclesDataForApplication($id, $filters);
    }

    public function formatFilters($query)
    {
        $filters = [
            'page' => isset($query['page']) ? $query['page'] : 1,
            'limit' => isset($query['limit']) ? $query['limit'] : 10,
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

        if (!isset($query['includeRemoved']) || $query['includeRemoved'] != '1') {
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

        return $filters;
    }
}
