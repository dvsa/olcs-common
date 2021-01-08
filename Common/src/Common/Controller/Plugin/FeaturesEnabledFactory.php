<?php

namespace Common\Controller\Plugin;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
