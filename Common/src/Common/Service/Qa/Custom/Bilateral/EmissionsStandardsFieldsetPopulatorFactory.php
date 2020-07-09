<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EmissionsStandardsFieldsetPopulatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return EmissionsStandardsFieldsetPopulator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new EmissionsStandardsFieldsetPopulator(
            $serviceLocator->get('QaCommonWarningAdder'),
            $serviceLocator->get('Helper\Translation'),
            $serviceLocator->get('QaBilateralYesNoWithMarkupForNoPopulator'),
            $serviceLocator->get('QaBilateralYesNoValueOptionsGenerator')
        );
    }
}
