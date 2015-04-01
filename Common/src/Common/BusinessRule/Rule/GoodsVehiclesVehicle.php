<?php

/**
 * Goods Vehicles Vehicle Rule
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessRule\Rule;

use Common\BusinessRule\BusinessRuleInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Goods Vehicles Vehicle Rule
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GoodsVehiclesVehicle implements BusinessRuleInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Action data map
     *
     * @var array
     */
    protected $vehicleDataMap = array(
        'main' => array(
            'mapFrom' => array(
                'data'
            ),
            'children' => array(
                'licence-vehicle' => array(
                    'mapFrom' => array(
                        'licence-vehicle'
                    )
                )
            )
        )
    );

    public function validate($data, $mode)
    {
        $data = $this->getServiceLocator()->get('Helper\Data')
            ->processDataMap($data, $this->vehicleDataMap);

        if ($mode !== 'add') {
            unset($data['vrm']);
        }

        return $data;
    }
}
