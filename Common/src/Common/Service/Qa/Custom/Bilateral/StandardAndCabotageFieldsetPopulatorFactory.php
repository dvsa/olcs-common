<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StandardAndCabotageFieldsetPopulatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StandardAndCabotageFieldsetPopulator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new StandardAndCabotageFieldsetPopulator(
            $serviceLocator->get('QaBilateralRadioFactory'),
            $serviceLocator->get('QaBilateralStandardAndCabotageYesNoRadioFactory'),
            $serviceLocator->get('QaBilateralYesNoRadioOptionsApplier')
        );
    }
}
