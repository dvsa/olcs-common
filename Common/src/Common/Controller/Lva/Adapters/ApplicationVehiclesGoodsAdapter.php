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
                'version' => $data['version'],
                'hasEnteredReg' => $data['hasEnteredReg'] === 'N' ? 'N' : 'Y'
            )
        );
    }

    /**
     * Do we need to show filters for vehciles
     */
    public function showFilters()
    {
        return true;
    }

    /**
     * Retrieve the filter form
     */
    public function getFilterForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $form = $formHelper->createForm('Lva\VehicleFilter');
        $vrmOptions = array_merge(['All' => 'All'], array_combine(range('A', 'Z'), range('A', 'Z')));
        $form->get('vrm')->setValueOptions($vrmOptions);
        return $form;
    }

    /**
     * Get all relevant form filters
     */
    public function getFilters($params)
    {
        $filters = [];
        $filters['vrm'] = isset($params['vrm']) ? $params['vrm'] : 'All';
        $filters['specified'] = isset($params['specified']) ? $params['specified'] : 'A';
        $filters['includeRemoved'] = isset($params['includeRemoved']) ? $params['includeRemoved'] : 0;
        $filters['disc'] = isset($params['disc']) ? $params['disc'] : 'A';
        return $filters;
    }
}
