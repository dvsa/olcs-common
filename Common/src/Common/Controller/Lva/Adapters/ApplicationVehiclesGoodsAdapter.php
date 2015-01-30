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
    /**
     * Get vehicles data for the given resource
     *
     * @param int $id
     * @return array
     */
    public function getVehiclesData($id)
    {
        return $this->getServiceLocator()->get('Entity\Licence')->getVehiclesDataForApplication($id);
    }

    /**
     * Save data
     *
     * @NOTE Possibly sharable between PSV too
     *
     * @param array $data
     * @return mixed
     */
    public function save($data, $id)
    {
        $data['data']['id'] = $id;

        return $this->getServiceLocator()->get('Entity\Application')->save($data['data']);
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
                'version'       => $data['version'],
                'hasEnteredReg' => isset($data['hasEnteredReg']) && ($data['hasEnteredReg'] == 'Y' ||
                    $data['hasEnteredReg'] == 'N') ? $data['hasEnteredReg'] : 'Y'
            )
        );
    }
}
