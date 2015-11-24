<?php

namespace Common\Rbac\Role;

use Dvsa\Olcs\Utils\Auth\AuthHelper;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
        $qs = AuthHelper::isOpenAm() ? 'QuerySender' : 'AnonQuerySender';

        $serviceLocator = $serviceLocator->getServiceLocator();
        return new RoleProvider($serviceLocator->get($qs));
    }
}
