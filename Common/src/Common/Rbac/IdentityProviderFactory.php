<?php

/**
 * Identity Provider Factory
 */
namespace Common\Rbac;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
            $serviceLocator->get('QuerySender')
        );
    }
}
