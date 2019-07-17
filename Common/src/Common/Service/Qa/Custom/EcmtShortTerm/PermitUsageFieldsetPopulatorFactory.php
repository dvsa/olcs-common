<?php

namespace Common\Service\Qa\Custom\EcmtShortTerm;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PermitUsageFieldsetPopulatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PermitUsageFieldsetPopulatorFactory
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new PermitUsageFieldsetPopulator(
            $serviceLocator->get('QaRadioFieldsetPopulator'),
            $serviceLocator->get('Helper\Translation')
        );
    }
}
