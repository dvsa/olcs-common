<?php

/**
 * Identity Provider Factory
 */
namespace Common\Rbac;

use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Session\Container;

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
            $serviceLocator->get('Request'),
            $serviceLocator->get(CacheEncryption::class)
        );
    }
}
