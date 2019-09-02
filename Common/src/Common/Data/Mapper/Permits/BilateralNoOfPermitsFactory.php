<?php

namespace Common\Data\Mapper\Permits;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BilateralNoOfPermitsFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return BilateralNoOfPermits
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new BilateralNoOfPermits(
            $serviceLocator->get('Helper\Translation')
        );
    }
}
