<?php

namespace Common\Rbac\Role;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class RoleProviderFactory
 * @package Common\Rbac\Role
 */
class RoleProviderFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RoleProvider
    {
        $serviceLocator = $container->getServiceLocator();
        return new RoleProvider($serviceLocator->get('QuerySender'));
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): RoleProvider
    {
        return $this->__invoke($serviceLocator, RoleProvider::class);
    }
}
