<?php

namespace Common\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FeaturesEnabledFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FeaturesEnabled
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FeaturesEnabled
    {
        return new FeaturesEnabled(
            $serviceLocator->getServiceLocator()->get('QuerySender')
        );
    }
}
