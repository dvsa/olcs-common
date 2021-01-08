<?php

namespace Common\Rbac\Role;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class RoleProviderFactory
 * @package Common\Rbac\Role
 */
class RoleProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();
        return new RoleProvider($serviceLocator->get('QuerySender'));
    }
}
