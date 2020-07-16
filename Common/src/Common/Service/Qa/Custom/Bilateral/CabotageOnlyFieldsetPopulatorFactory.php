<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CabotageOnlyFieldsetPopulatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CabotageOnlyFieldsetPopulator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new CabotageOnlyFieldsetPopulator(
            $serviceLocator->get('Helper\Translation'),
            $serviceLocator->get('QaBilateralYesNoWithMarkupForNoPopulator'),
            $serviceLocator->get('QaBilateralStandardYesNoValueOptionsGenerator')
        );
    }
}
