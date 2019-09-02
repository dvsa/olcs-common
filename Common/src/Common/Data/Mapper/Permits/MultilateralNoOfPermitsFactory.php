<?php

namespace Common\Data\Mapper\Permits;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MultilateralNoOfPermitsFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return MultilateralNoOfPermits
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new MultilateralNoOfPermits(
            $serviceLocator->get('Helper\Translation')
        );
    }
}
