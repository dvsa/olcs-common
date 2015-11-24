<?php

namespace Common\Rbac;

use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

/**
 * Class UserProviderDelegatorFactory
 *
 * @todo this is a stop-gap to bridge between zfcuser and openam
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
