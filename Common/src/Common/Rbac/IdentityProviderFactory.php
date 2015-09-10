<?php

namespace Common\Rbac;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
            $serviceLocator->get('TransferAnnotationBuilder'),
            $serviceLocator->get('QueryService')
        );
    }
}