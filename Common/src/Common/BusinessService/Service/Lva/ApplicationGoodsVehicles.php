<?php

/**
 * Application Goods Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;

/**
 * Application Goods Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationGoodsVehicles implements BusinessServiceInterface, BusinessServiceAwareInterface
{
    use BusinessServiceAwareTrait;

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        return $this->getBusinessServiceManager()->get('Lva\Application')->process($params);
    }
}
