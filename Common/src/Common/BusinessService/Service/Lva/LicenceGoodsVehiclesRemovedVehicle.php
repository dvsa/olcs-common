<?php

/**
 *  Licence Goods Vehicles Removed Vehicle
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessRule\BusinessRuleAwareInterface;
use Common\BusinessRule\BusinessRuleAwareTrait;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;
use Common\BusinessService\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 *  Licence Goods Vehicles Removed Vehicle
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceGoodsVehiclesRemovedVehicle implements
    BusinessServiceInterface,
    BusinessRuleAwareInterface,
    BusinessServiceAwareInterface,
    ServiceLocatorAwareInterface
{
    use BusinessRuleAwareTrait,
        ServiceLocatorAwareTrait,
        BusinessServiceAwareTrait;

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $validatedData = $this->getBusinessRuleManager()
            ->get('LicenceGoodsVehiclesRemovedVehicle')
            ->validate($params['data']['licence-vehicle']);
        $response = new Response();
        if (!$validatedData) {
            $response->setType(Response::TYPE_FAILED);
            $response->setMessage('internal-vehicle-licence-wrong-removal-date');
        } else {
            $this->getServiceLocator()->get('Entity\LicenceVehicle')->save($validatedData);
            $response->setType(Response::TYPE_SUCCESS);
        }
        return $response;
    }
}
