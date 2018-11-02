<?php

namespace Common\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
