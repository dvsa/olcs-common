<?php

/**
 * Identity Provider Factory
 */
namespace Common\Rbac;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

/**
 * Identity Provider Factory
 */
class IdentityProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new IdentityProvider(
            $serviceLocator->get('QuerySender'),
            new Container('user_details'),
            $serviceLocator->get('Request')
        );
    }
}
