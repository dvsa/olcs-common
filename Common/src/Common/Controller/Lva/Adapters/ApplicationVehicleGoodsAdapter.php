<?php

/**
 * Application Vehicle Goods Adapter
 *
 * @author Alex Peshkov <alex.pehkov@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Interfaces\VehicleGoodsAdapterInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Application Vehicle Goods Adapter
 *
 * @author Alex Peshkov <alex.pehkov@valtech.co.uk>
 */
class ApplicationVehicleGoodsAdapter implements
    VehicleGoodsAdapterInterface,
    ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

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

    /**
     * Populate form with data
     * 
     * @param Request $request
     * @param array $entityData
     * @param Zend\Form\Form
     * @return Zend\Form\Form
     */
    public function populateForm($request, $entityData, $form)
    {
        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->formatDataForForm($entityData);
        }
        $form->setData($data);
        return $form;
    }
}
