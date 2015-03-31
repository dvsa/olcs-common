<?php

/**
 * Request Disc
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Request Disc
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class RequestDisc implements
    BusinessServiceInterface,
    ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $discData = ['licenceVehicle' => $params['licenceVehicle'], 'isCopy' => $params['isCopy']];

        $this->getServiceLocator()->get('Entity\GoodsDisc')->save($discData);

        $response = new Response();
        $response->setType(Response::TYPE_SUCCESS);
        return $response;
    }
}
