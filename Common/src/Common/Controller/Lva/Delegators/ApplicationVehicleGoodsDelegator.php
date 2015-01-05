<?php

/**
 * Application Vehicle Goods Delegator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Controller\Lva\Delegators;

use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Common\Controller\Lva\Interfaces\ControllerAwareInterface;

/**
 * Application Vehicle Goods Delegator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ApplicationVehicleGoodsDelegator implements DelegatorFactoryInterface
{
    /**
     * A factory that creates delegates of a given service
     *
     * @param ServiceLocatorInterface $serviceLocator the service locator which requested the service
     * @param string                  $name           the normalized service name
     * @param string                  $requestedName  the requested service name
     * @param callable                $callback       the callback that is responsible for creating the service
     *
     * @return mixed
     */
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        $controller = $callback();

        $adapter = $serviceLocator->getServiceLocator()->get('ApplicationVehicleGoodsAdapter');

        $controller->setVehicleGoodsAdapter($adapter);

        return $controller;
    }
}
