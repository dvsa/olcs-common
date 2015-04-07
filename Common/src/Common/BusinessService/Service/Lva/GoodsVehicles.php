<?php

/**
 * Goods Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\Response;

/**
 * Goods Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GoodsVehicles implements BusinessServiceInterface
{

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $response = new Response();
        $response->setType(Response::TYPE_NO_OP);
        return $response;
    }
}
