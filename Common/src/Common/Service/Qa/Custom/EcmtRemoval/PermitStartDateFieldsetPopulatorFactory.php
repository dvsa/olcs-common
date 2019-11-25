<?php

namespace Common\Service\Qa\Custom\EcmtRemoval;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PermitStartDateFieldsetPopulatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PermitStartDateFieldsetPopulator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new PermitStartDateFieldsetPopulator(
            $serviceLocator->get('QaBaseDateFieldsetPopulator'),
            $serviceLocator->get('Helper\Translation')
        );
    }
}
