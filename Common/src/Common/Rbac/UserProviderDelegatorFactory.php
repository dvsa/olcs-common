<?php

namespace Common\Rbac;

use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

/**
 * Class UserProviderDelegatorFactory
 *
 * @todo Remove this class when we are fully integrated with OpenAM
 *
 * @package Common\Rbac
 */
class UserProviderDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * A factory that creates delegates of a given service
     *
     * @param ServiceLocatorInterface $serviceLocator the service locator which requested the service
     * @param string $name the normalized service name
     * @param string $requestedName the requested service name
     * @param callable $callback the callback that is responsible for creating the service
     *
     * @return mixed
     */
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        $service = new UserProvider($serviceLocator->get('AnonQuerySender'), new Container('user_details'));

        return $service;
    }
}
