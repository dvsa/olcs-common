<?php

namespace Common\Controller\Plugin;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FeaturesEnabledForMethodFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FeaturesEnabledForMethod
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FeaturesEnabledForMethod
    {
        return new FeaturesEnabledForMethod(
            $serviceLocator->getServiceLocator()->get('QuerySender')
        );
    }
}
