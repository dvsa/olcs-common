<?php

namespace Common\Service\Qa\Custom\CertRoadworthiness;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MotExpiryDateFieldsetPopulatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return MotExpiryDateFieldsetPopulator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new MotExpiryDateFieldsetPopulator(
            $serviceLocator->get('Helper\Translation')
        );
    }
}
